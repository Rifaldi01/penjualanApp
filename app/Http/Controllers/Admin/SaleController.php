<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccesoriesCategory;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\Bank;
use App\Models\CategoryItem;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\ItemSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil data penjualan
        $sales = Sale::where('divisi_id', Auth::user()->divisi_id)->with(['customer.divisi', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();

        // Format nomor invoice untuk setiap transaksi

       $bank = Bank::all();
        // Pass data ke view
        return view('admin.sale.index', compact('sales', 'bank'));
    }

    private function convertToRoman($monthNumber)
    {
        $months = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $months[$monthNumber];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accessories = Accessories::all();
        $item = Item::all();
        $customer = Customer::where('divisi_id', Auth::user()->divisi_id)->get();
        return view('admin.sale.create', compact('accessories', 'item', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $divisi = Divisi::find(Auth::user()->divisi_id);

        if (!$divisi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Divisi tidak ditemukan untuk pengguna yang sedang login.'
            ], 400);
        }

        $currentYear = date('Y');
        $currentMonthNumber = str_pad(date('n'), 2, '0', STR_PAD_LEFT); // Bulan selalu dua digit

        // Hitung jumlah transaksi pada divisi terkait untuk tahun ini
        $transactionCount = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->whereYear('created_at', $currentYear) // Hanya menghitung transaksi dalam tahun yang sama
            ->count();

        $nextNumber = str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT); // Nomor urut dengan 4 digit

        // Format nomor invoice
        $invoiceNumber = "INV/{$divisi->inv_format}/{$nextNumber}/{$currentMonthNumber}/{$currentYear}";


        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'total_item' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'ongkir' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'accessories' => 'nullable|array',
            'items' => 'nullable|array'
        ]);

        DB::beginTransaction();

        try {
            // Create sale record
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'total_item' => $validated['total_item'],
                'total_price' => $validated['total_price'],
                'ongkir' => $validated['ongkir'],
                'diskon' => $validated['diskon'],
                'pay' => $validated['bayar'],
                'ppn' => $request->ppn,
                'pph' => $request->pph,
                'nominal_in' => $request->nominal_in,
                'deadlines' => $request->deadlines,
                'user_id' => Auth::id(),
                'divisi_id' => Auth::user()->divisi_id,
                'invoice' => $invoiceNumber
            ]);

            // Handle nominal_in for debts
            if (!empty($request->nominal_in) && $request->nominal_in > 0) {
                Debt::create([
                    'sale_id' => $sale->id,
                    'pay_debts' => $request->nominal_in,
                    'date_pay' => now()
                ]);
            }

            // Save Accessories sale and update stock
            if ($request->has('accessories')) {
                foreach ($validated['accessories'] as $accessory) {
                    $accessoryRecord = Accessories::find($accessory['accessories_id']);

                    if ($accessoryRecord) {
                        if ($accessory['qty'] > $accessoryRecord->stok) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => "Stok kurang untuk aksesori dengan ID {$accessory['accessories_id']}"
                            ], 400);
                        }

                        // Update stock
                        $accessoryRecord->stok -= $accessory['qty'];
                        $accessoryRecord->save();

                        // Save Accessories sale
                        AccessoriesSale::create([
                            'sale_id' => $sale->id,
                            'accessories_id' => $accessory['accessories_id'],
                            'qty' => $accessory['qty'],
                            'subtotal' => $accessory['subtotal'],
                            'acces_out' => now()
                        ]);
                    }
                }
            }

            // Save Item sale and remove from items
            if ($request->has('items')) {
                foreach ($validated['items'] as $item) {
                    $itemRecord = Item::where('itemcategory_id', $item['itemcategory_id'])
                        ->where('no_seri', $item['no_seri'])
                        ->first();

                    if ($itemRecord) {
                        ItemSale::create([
                            'sale_id' => $sale->id,
                            'itemcategory_id' => $item['itemcategory_id'],
                            'name' => $item['name'],
                            'no_seri' => $item['no_seri'],
                            'price' => $item['price'],
                            'divisi_id' => $sale->divisi_id,
                            'capital_price' => $itemRecord->capital_price,
                            'date_in' => $itemRecord->created_at
                        ]);

                        // Remove item from items
                        $itemRecord->delete();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sale saved successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the sale: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nominal_in'         => 'required',
            'pay_debts'        => 'required|',
            'date_pay'       => 'required',
        ]);

        if (empty($request->input('bank_id')) && empty($request->input('description'))) {
            return back()->withErrors([
                'bank_id' => 'Kolom Bank atau Lainya harus diisi.',
            ])->withInput();
        }

        if (!empty($request->input('bank_id')) && empty($request->input('penerima'))) {
            return back()->withErrors([
                'penerima' => 'Masukan Nama Penerima.',
            ])->withInput();
        }
        // Format ulang input nominal_in dan pay_debts untuk menghapus simbol dan titik
        $nominal_in = str_replace(['Rp.', '.', ' '], '', $request->input('nominal_in'));
        $pay_debts = str_replace(['Rp.', '.', ' '], '', $request->input('pay_debts'));

        // Update nominal_in di tabel sales
        $sale = Sale::findOrFail($id);
        $sale->nominal_in = $nominal_in;
        $sale->save();

        // Simpan data ke tabel debts
        $debts = Debt::create([
            'sale_id' => $id,
            'bank_id' => $request->input('bank_id'),
            'pay_debts' => $pay_debts,
            'date_pay' => $request->input('date_pay'),
            'penerima' => $request->input('penerima'),
            'description' => $request->input('description'),
        ]);

        return back()->withSuccess('Pembayaran Berhasil');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction(); // Mulai transaksi database

        try {
            // Ambil data sale berdasarkan id
            $sale = Sale::findOrFail($id);

            // Proses pengembalian item_sales ke table items
            $itemSales = ItemSale::where('sale_id', $id)->get();
            foreach ($itemSales as $itemSale) {
                // Buat ulang data item berdasarkan item_sales
                Item::create([
                    'divisi_id' => $itemSale->divisi_id,
                    'itemcategory_id' => $itemSale->itemcategory_id,
                    'name' => $itemSale->name,
                    'price' => $itemSale->price,
                    'capital_price' => $itemSale->capital_price,
                    'no_seri' => $itemSale->no_seri,
                    'status' => 1, // Ubah status item ke tersedia
                ]);

                // Hapus data dari item_sales
                $itemSale->delete();
            }

            // Proses pengembalian stok accessories
            $accessoriesSales = AccessoriesSale::where('sale_id', $id)->get();
            foreach ($accessoriesSales as $accessorySale) {
                // Tambahkan stok kembali di tabel accessories
                $accessory = Accessories::find($accessorySale->accessories_id);
                if ($accessory) {
                    $accessory->stok += $accessorySale->qty;
                    $accessory->save();
                }

                // Hapus data dari accessories_sales
                $accessorySale->delete();
            }

            // Hapus data sale
            $sale->delete();

            DB::commit(); // Commit transaksi jika semua berhasil

            // Kembalikan respons JSON untuk AJAX
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibatalkan']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika ada error

            // Kembalikan respons JSON untuk error
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat membatalkan transaksi: ' . $e->getMessage()], 500);
        }
    }



    public function fetchData(Request $request)
    {
        $codeSale = $request->get('code');

        // Ambil accessory berdasarkan divisi dan kode akses, pastikan harga > 0
        $accessory = Accessories::where('divisi_id', Auth::user()->divisi_id)
            ->where('code_acces', $codeSale)
            ->first();

        // Ambil item berdasarkan divisi, no_seri, status 0, dan harga > 0
        $item = Item::where('divisi_id', Auth::user()->divisi_id)
            ->where('no_seri', $codeSale)
            ->where('status', 0)
            ->where('price', '>', 0)
            ->first();

        if ($accessory) {
            return response()->json(['status' => 'success', 'type' => 'accessory', 'data' => $accessory]);
        } elseif ($item) {
            return response()->json(['status' => 'success', 'type' => 'item', 'data' => $item]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Accessories / Item Tidak ditemukan']);
        }
    }

}
