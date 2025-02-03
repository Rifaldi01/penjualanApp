<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\Bank;
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
        $sales = Sale::with(['divisi','customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();

        // Format nomor invoice untuk setiap transaksi
        foreach ($sales as $data) {
            $transactionCount = Sale::where('id', '<=', $data->id)->count();
            $nextNumber = str_pad($transactionCount, 4, '0', STR_PAD_LEFT);
            $currentYear = date('Y');
            $currentMonthNumber = date('n');
            $currentMonthRoman = $this->convertToRoman($currentMonthNumber);

            // Format nomor invoice
            $data->invoiceNumber = "INV/DND/{$nextNumber}/{$currentMonthRoman}/{$currentYear}";
        }
        $bank = Bank::all();
        $divisi = Divisi::all();
        // Pass data ke view
        return view('manager.sale.index', compact('sales', 'bank', 'divisi'));
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
        $customer = Customer::all();
        $divisi = Divisi::all();
        return view('manager.sale.create', compact('accessories', 'item', 'customer', 'divisi'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Ambil informasi divisi dan kode pilihan
        $divisiId = $request->input('divisi_id');
        $kodePilihan = $request->input('kode'); // Misalnya ADJ
        $currentYear = date('Y');
        $currentMonthNumber = str_pad(date('n'), 2, '0', STR_PAD_LEFT); // Bulan selalu dua digit

        // Hitung jumlah transaksi pada divisi terkait untuk tahun ini
        $transactionCount = Sale::where('divisi_id', $divisiId)
            ->whereYear('created_at', $currentYear) // Hanya menghitung transaksi dalam tahun yang sama
            ->count();

        // Tentukan nomor urut dengan 4 digit
        $nextNumber = str_pad($transactionCount + 1, 4, '0', STR_PAD_LEFT); // Nomor urut dengan 4 digit
        // Format nomor invoice
        $invoiceNumber = "INV/{$kodePilihan}/{$nextNumber}/{$currentMonthNumber}/{$currentYear}";

        // Periksa apakah invoice sudah ada
        while (Sale::where('invoice', $invoiceNumber)->exists()) {
            // Jika ada invoice yang sama, tambah nomor urutnya
            $nextNumber = str_pad($transactionCount + 2, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV/{$kodePilihan}/{$nextNumber}/{$currentMonthNumber}/{$currentYear}";
        }

        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'divisi_id' => 'required',
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
            // Simpan transaksi penjualan
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
                'divisi_id' => $validated['divisi_id'],
                'invoice' => $invoiceNumber
            ]);

            // Simpan hutang jika ada nominal_in
            if (!empty($request->nominal_in) && $request->nominal_in > 0) {
                Debt::create([
                    'sale_id' => $sale->id,
                    'pay_debts' => $request->nominal_in,
                    'date_pay' => now()
                ]);
            }

            // Simpan Accessories Sale dan update stok
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

                        // Update stok
                        $accessoryRecord->stok -= $accessory['qty'];
                        $accessoryRecord->save();

                        // Simpan transaksi Accessories
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

            // Simpan Item Sale dan hapus dari daftar item
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

                        // Hapus item dari daftar item
                        $itemRecord->delete();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sale saved successfully.',
                'invoice' => $invoiceNumber
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
    public function edit($id) //ieu fungsi jang ngambil form edit?enya
    {
        if (\request()->ajax()){
            $sale = Sale::with(['itemSales.itemCategory', 'accessoriesSales.accessories'])->findOrFail($id);
            return response()->json($sale);
        }
        $customers = Customer::all();
        $sale = Sale::with(['itemSales.itemCategory', 'accessoriesSales.accessories'])->findOrFail($id);
        return view('manager.sale.edit', compact('sale', 'customers'));
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
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);
            $sale->customer_id = $request->customer_id;
            $sale->total_price = str_replace('.', '', $request->total_price); // Total harga tanpa titik
            $sale->diskon = $request->diskon;
            $sale->ongkir = $request->ongkir;
            $sale->nominal_in = $request->nominal_in;
            $sale->deadlines = $request->deadlines;
            $sale->total_item = $request->total_item;
            $sale->pay = str_replace('.', '', $request->bayar); // Bayar tanpa titik
            $sale->save();
            ItemSale::where('sale_id', $id);
            AccessoriesSale::where('sale_id', $id);
            Debt::where('sale_id', $id);

            if ($request->nominal_in == 0) {
                // Hapus semua hutang terkait jika nominal_in = 0
                Debt::where('sale_id', $sale->id)->delete();
            } else {
                // Periksa apakah ada hutang pada tanggal yang sama
                $debt = Debt::where('sale_id', $sale->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->first();

                if ($debt) {
                    // Jika ada, perbarui hutang
                    $debt->pay_debts = $request->nominal_in;
                    $debt->date_pay = now();
                    $debt->save();
                } else {
                    // Jika tidak ada, buat data hutang baru
                    Debt::create([
                        'sale_id' => $sale->id,
                        'pay_debts' => $request->nominal_in,
                        'date_pay' => now(),
                    ]);
                }
            }

            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    // Temukan item di tabel items berdasarkan no_seri
                    $existingItem = Item::where('no_seri', $item['no_seri'])->first();
                    if ($existingItem) {
                        // Pindahkan data ke tabel item_sales
                        $itemSale = new ItemSale();
                        $itemSale->sale_id = $sale->id;
                        $itemSale->name = $existingItem->name;
                        $itemSale->no_seri = $existingItem->no_seri;
                        $itemSale->price = $existingItem->price;
                        $itemSale->capital_price = $existingItem->capital_price;
                        $itemSale->itemcategory_id = $existingItem->itemcategory_id;
                        $itemSale->date_in = now();
                        $itemSale->save();

                        // Hapus item dari tabel items
                        $existingItem->delete();
                    }
                }
            }



            if ($request->has('accessories')) {
                foreach ($request->accessories as $accessory) {
                    $id = $accessory['id'] ?? null;
                    $accessoriesSale = AccessoriesSale::firstOrNew(['id' => $id]);

                    // Dapatkan aksesori terkait berdasarkan accessories_id
                    $accessoryModel = Accessories::find($accessory['accessories_id']);
                    if ($accessoryModel) {
                        // Dapatkan qty lama jika aksesori sudah ada, jika tidak, set 0 (untuk aksesori baru)
                        $previousQty = $accessoriesSale->exists ? $accessoriesSale->qty : 0;
                        $newQty = $accessory['qty'];

                        // Jika aksesori baru, kurangi stok berdasarkan qty baru
                        if (!$accessoriesSale->exists) {
                            $accessoryModel->stok -= $newQty;
                        } else {
                            // Jika aksesori lama, hitung perbedaan stok berdasarkan perubahan qty
                            $stockDifference = $previousQty - $newQty;
                            $accessoryModel->stok += $stockDifference;
                        }

                        // Simpan perubahan stok
                        $accessoryModel->save();

                        // Simpan atau perbarui AccessoriesSale
                        $accessoriesSale->sale_id = $sale->id;
                        $accessoriesSale->accessories_id = $accessory['accessories_id'];
                        $accessoriesSale->qty = $newQty;
                        $accessoriesSale->subtotal = str_replace('.', '', $accessory['subtotal']);
                        $accessoriesSale->acces_out = now();
                        $accessoriesSale->save();
                    }
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction updated successfully!',
                'reqest' => $request->all()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update transaction! ' . $e->getMessage(),
                'request' => $request->all() //save
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Cari itemSale berdasarkan ID
            $itemSale = ItemSale::find($id);
            $accessorySale = AccessoriesSale::find($id);

            if ($itemSale) {
                // Pindahkan data itemSale ke tabel items
                // Mencari item berdasarkan category_id
                $item = Item::where('id', $itemSale->itemcategory_id)->first();

                // Jika item tidak ditemukan, buat item baru
                if (!$item) {
                    // Membuat item baru dengan data dari itemSale
                    $item = Item::create([
                        'itemcategory_id' => $itemSale->itemcategory_id,
                        'name' => $itemSale->name,
                        'price' => $itemSale->price,
                        'capital_price' => $itemSale->capital_price,
                        'no_seri' => $itemSale->no_seri,
                        'status' => 0 // Pastikan status sesuai dengan yang diinginkan
                    ]);
                }

                // Hapus item dari tabel item_sales
                $itemSale->delete();

            } elseif ($accessorySale) {
                // Kembalikan aksesori ke stok dan hapus dari accessories_sales
                $accessory = Accessories::find($accessorySale->accessories_id);

                if ($accessory) {
                    // Tambahkan qty ke stok
                    $accessory->stok += $accessorySale->qty;
                    $accessory->save();
                }

                // Hapus aksesori dari tabel accessories_sales
                $accessorySale->delete();

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Item berhasil dipindahkan dan dihapus.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function fetchData(Request $request)
    {
        $codeSale = $request->get('code');
        $accessory = Accessories::where('code_acces', $codeSale)->first();
        $item = Item::where('no_seri', $codeSale)->where('status', 0)->first();

        if ($accessory) {
            return response()->json(['status' => 'success', 'type' => 'accessory', 'data' => $accessory]);
        } elseif ($item) {
            return response()->json(['status' => 'success', 'type' => 'item', 'data' => $item]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Code Accessories or No Seri not found!']);
        }
    }
    public function bayar(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nominal_in'         => 'required',
            'pay_debts'        => 'required|',
            'date_pay'       => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
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
            'description' => $request->input('description'),
        ]);

        return back()->withSuccess('Pembayaran Berhasil');
    }
}
