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
        $bank = Bank::all();
        return view('manager.sale.create', compact('accessories', 'item', 'customer', 'divisi', 'bank'));
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
            'items' => 'nullable|array',
            'created_at' => 'required',
        ], [
            'customer_id.required' => 'Pelanggan wajib diisi.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak valid.',

            'divisi_id.required' => 'Divisi wajib diisi.',

            'total_item.required' => 'Total item wajib diisi.',
            'total_item.integer' => 'Total item harus berupa angka.',
            'total_item.min' => 'Total item minimal harus 1.',

            'total_price.required' => 'Total harga wajib diisi.',
            'total_price.numeric' => 'Total harga harus berupa angka.',
            'total_price.min' => 'Total harga tidak boleh kurang dari 0.',

            'ongkir.required' => 'Ongkos kirim wajib diisi.',
            'ongkir.numeric' => 'Ongkos kirim harus berupa angka.',
            'ongkir.min' => 'Ongkos kirim tidak boleh kurang dari 0.',

            'diskon.required' => 'Diskon wajib diisi.',
            'diskon.numeric' => 'Diskon harus berupa angka.',
            'diskon.min' => 'Diskon tidak boleh kurang dari 0.',

            'bayar.required' => 'Jumlah bayar wajib diisi.',
            'bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'bayar.min' => 'Jumlah bayar tidak boleh kurang dari 0.',

            'creates_at.required' => 'Tanggal transaksi wajib diisi.',
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
                'no_po' => $request->no_po,
                'fee' => $request->fee,
                'user_id' => Auth::id(),
                'divisi_id' => $validated['divisi_id'],
                'created_at' => $validated['created_at'],
                'invoice' => $invoiceNumber
            ]);

            // Simpan hutang jika ada nominal_in
            Debt::create([
                'sale_id' => $sale->id,
                'pay_debts' => $sale->nominal_in,
                'bank_id' => $request->bank_id,
                'penerima' => $request->penerima,
                'description' => $request->description,
                'date_pay' => now()
            ]);

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
            $sale = Sale::with(['itemSales.itemCategory', 'accessoriesSales.accessories', 'debt.bank'])->findOrFail($id);
            return response()->json($sale);
        }
        $customers = Customer::all();
        $sale = Sale::with(['itemSales.itemCategory', 'accessoriesSales.accessories', 'divisi', 'debt.bank'])->findOrFail($id);
        $divisi = Divisi::all();
        $bank = Bank::all();
        return view('manager.sale.edit', compact('sale', 'customers', 'divisi', 'bank'));
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
            $sale->nominal_in = str_replace('.', '', $request->nominal_in);;
            $sale->deadlines = $request->deadlines;
            $sale->total_item = $request->total_item;
            $sale->created_at = $request->created_at;
            $sale->no_po = $request->no_po;
            $sale->divisi_id = $request->divisi_id;
            $sale->fee = str_replace('.', '', $request->bayar); // Bayar tanpa titik
            $sale->pay = str_replace('.', '', $request->bayar); // Bayar tanpa titik
            $sale->ppn = str_replace('.', '', $request->ppn); // Bayar tanpa titik
            $sale->pph = str_replace('.', '', $request->pph); // Bayar tanpa titik
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
                    $debt->bank_id = $request->bank_id;
                    $debt->penerima = $request->penerima;
                    $debt->description = $request->description;
                    $debt->date_pay = now();
                    $debt->save();
                } else {
                    // Jika tidak ada, buat data hutang baru
                    Debt::create([
                        'sale_id' => $sale->id,
                        'pay_debts' => $request->nominal_in,
                        'bank_id' => $request->bank_id,
                        'penerima' => $request->penerima,
                        'description' => $request->description,
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

            // Hapus data dari tabel debts yang memiliki sale_id yang sama
            Debt::where('sale_id', $id)->delete();

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
