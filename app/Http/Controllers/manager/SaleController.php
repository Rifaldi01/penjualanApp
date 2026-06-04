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
use App\Models\ReturSale;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnAccessories;
use App\Models\SalesReturnItem;
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
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $year = $request->year ?? date('Y');

        $month = $request->month ?? date('n');

        $divisi_id = $request->divisi_id;

        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $sales = Sale::with([
            'divisi',
            'customer',
            'user',
            'itemSales.itemCategory',
            'accessoriesSales.accessories'
        ])

            /*
            |--------------------------------------------------------------------------
            | FILTER TAHUN
            |--------------------------------------------------------------------------
            */

            ->when($year != 'all', function ($query) use ($year) {

                $query->whereYear('created_at', $year);

            })

            /*
            |--------------------------------------------------------------------------
            | FILTER BULAN
            |--------------------------------------------------------------------------
            */

            ->when($month != 'all', function ($query) use ($month) {

                $query->whereMonth('created_at', $month);

            })

            /*
            |--------------------------------------------------------------------------
            | FILTER DIVISI
            |--------------------------------------------------------------------------
            */

            ->when($divisi_id && $divisi_id != 'all', function ($query) use ($divisi_id) {

                $query->where('divisi_id', $divisi_id);

            })

            ->latest()

            ->get();

        /*
        |--------------------------------------------------------------------------
        | FORMAT INVOICE
        |--------------------------------------------------------------------------
        */

        foreach ($sales as $data) {

            $transactionCount = Sale::where('id', '<=', $data->id)->count();

            $nextNumber = str_pad($transactionCount, 4, '0', STR_PAD_LEFT);

            $currentYear = date('Y', strtotime($data->created_at));

            $currentMonthNumber = date('n', strtotime($data->created_at));

            $currentMonthRoman = $this->convertToRoman($currentMonthNumber);

            $data->invoiceNumber = "INV/DND/{$nextNumber}/{$currentMonthRoman}/{$currentYear}";
        }

        /*
        |--------------------------------------------------------------------------
        | DATA FILTER
        |--------------------------------------------------------------------------
        */

        $bank = Bank::all();

        $divisi = Divisi::all();

        $years = Sale::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */
        $salesactive = Sale::with(['divisi','customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();

        return view('manager.sale.index', compact(
            'sales',
            'bank',
            'divisi',
            'years',
            'salesactive'
        ));
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
                'admin_fee' => $request->admin_fee,
                'user_id' => Auth::id(),
                'divisi_id' => $validated['divisi_id'],
                'created_at' => $validated['created_at'],
                'invoice' => $invoiceNumber
            ]);

            // Simpan data hutang hanya jika nominal_in lebih dari 0
            if ((int) str_replace('.', '', $sale->nominal_in) > 0) {
                Debt::create([
                    'sale_id' => $sale->id,
                    'pay_debts' => $sale->nominal_in,
                    'bank_id' => $request->bank_id,
                    'penerima' => $request->penerima,
                    'description' => $request->description,
                    'date_pay' => $request->date_pay ?? now()
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
    public function edit($id)
    {
        if (request()->ajax()) {

            $sale = Sale::with([
                'itemSales' => function ($query) {
                    $query->where('status_return', 0);
                },
                'itemSales.itemCategory',
                'accessoriesSales' => function ($query) {
                    $query->where('status_return', 0);
                },
                'accessoriesSales.accessories',
                'debt.bank'
            ])->findOrFail($id);

            return response()->json($sale);
        }

        $customers = Customer::all();

        $sale = Sale::with([
            'itemSales' => function ($query) {
                $query->where('status_return', 0);
            },
            'itemSales.itemCategory',
            'accessoriesSales' => function ($query) {
                $query->where('status_return', 0);
            },
            'accessoriesSales.accessories',
            'divisi',
            'debt.bank'
        ])->findOrFail($id);

        $divisi = Divisi::all();
        $bank   = Bank::all();

        return view(
            'manager.sale.edit',
            compact('sale', 'customers', 'divisi', 'bank')
        );
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

            $sale = Sale::with('divisi')->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | UPDATE SALE
            |--------------------------------------------------------------------------
            */

            $sale->update([

                'customer_id' => $request->customer_id,
                'divisi_id'   => $request->divisi_id,

                'total_item'  => $request->total_item,
                'total_price' => $request->total_price,

                'diskon'      => $request->diskon ?? 0,
                'ongkir'      => $request->ongkir ?? 0,

                'ppn'         => $request->ppn ?? 0,
                'pph'         => $request->pph ?? 0,

                'admin_fee'   => $request->admin_fee ?? 0,

                'pay'         => $request->bayar,
                'nominal_in'  => $request->nominal_in,

                'deadlines'   => $request->deadlines,
                'no_po'       => $request->no_po,

                'fee'         => $request->fee ?? 0,

                'created_at'  => $request->created_at,
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE DEBT
            |--------------------------------------------------------------------------
            */

            if ($sale->debt()->exists()) {

                $sale->debt()->update([

                    'bank_id'     => $request->bank_id,
                    'penerima'    => $request->penerima,
                    'description' => $request->description,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE RETURN INVOICE
            |--------------------------------------------------------------------------
            */

            $divisiName = strtoupper(
                preg_replace('/\s+/', '', $sale->divisi->name)
            );

            $lastReturn = SalesReturn::latest()->first();

            $nextNumber = $lastReturn
                ? $lastReturn->id + 1
                : 1;

            $returnInvoice = 'RTR-' .
                $divisiName . '-' .
                date('Ymd') . '-' .
                str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            /*
            |--------------------------------------------------------------------------
            | ACCESSORIES
            |--------------------------------------------------------------------------
            */

            if ($request->accessories) {

                foreach ($request->accessories as $row) {

                    /*
                    |--------------------------------------------------------------------------
                    | ACCESSORIES LAMA
                    |--------------------------------------------------------------------------
                    */

                    if ($row['status'] == 'new') {

                        $accessory = Accessories::find(
                            $row['accessories_id']
                        );

                        if (!$accessory) {
                            continue;
                        }

                        if ($accessory->stok < $row['qty']) {

                            DB::rollBack();

                            return response()->json([
                                'status'  => 'error',
                                'message' => 'Stok accessories tidak cukup'
                            ]);
                        }

                        AccessoriesSale::create([

                            'sale_id'        => $sale->id,

                            'accessories_id' => $row['accessories_id'],

                            'qty'            => $row['qty'],

                            'subtotal'       =>
                                $accessory->price * $row['qty'],

                            'return_qty'     => 0,

                            'status_return'  => 0,

                            'acces_out'      => now(),

                        ]);

                        $accessory->decrement(
                            'stok',
                            $row['qty']
                        );
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | ACCESSORIES BARU
                    |--------------------------------------------------------------------------
                    */

                    if ($row['status'] == 'new') {

                        $accessory = Accessories::find(
                            $row['accessories_id']
                        );

                        if (!$accessory) {
                            continue;
                        }

                        if ($accessory->stok < $row['qty']) {

                            DB::rollBack();

                            return response()->json([
                                'status'  => 'error',
                                'message' => 'Stok accessories tidak cukup'
                            ]);
                        }

                        AccessoriesSale::create([

                            'sale_id'        => $sale->id,

                            'accessories_id' => $row['accessories_id'],

                            'qty'            => $row['qty'],

                            'subtotal'       =>
                                $accessory->price * $row['qty'],

                            'return_qty'     => 0,
                            'acces_out' => now(),

                        ]);

                        $accessory->decrement(
                            'stok',
                            $row['qty']
                        );
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ITEMS
            |--------------------------------------------------------------------------
            */

            if ($request->items) {

                foreach ($request->items as $row) {

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM BARU
                    |--------------------------------------------------------------------------
                    */

                    if ($row['status'] == 'new') {

                        $item = Item::where(
                            'no_seri',
                            $row['no_seri']
                        )->first();

                        if (!$item) {
                            continue;
                        }

                        if ($item->status == 2) {

                            DB::rollBack();

                            return response()->json([
                                'status'  => 'error',
                                'message' => 'Item sudah terjual'
                            ]);
                        }

                        ItemSale::create([

                            'sale_id'         => $sale->id,

                            'itemcategory_id' => $row['itemcategory_id'],

                            'name'            => $row['name'],

                            'price'           => $row['price'],

                            'no_seri'         => $row['no_seri'],

                            'status_return'   => 0

                        ]);

                        $item->update([
                            'status' => 2
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM LAMA
                    |--------------------------------------------------------------------------
                    */

                    if ($row['status'] == 'old') {

                        $detail = ItemSale::find(
                            $row['sale_detail_id']
                        );

                        if (!$detail) {
                            continue;
                        }

                        $detail->update([

                            'price' => $row['price']

                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([

                'status'  => 'success',

                'message' => 'Transaction berhasil diupdate'

            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'status'  => 'error',

                'message' => $e->getMessage()

            ]);
        }
    }
    public function returnAccessory(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $accessorySale = AccessoriesSale::with([
                'accessories',
                'sale'
            ])->find($id);

            if (!$accessorySale) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Data accessories sale tidak ditemukan'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI RETURN QTY
            |--------------------------------------------------------------------------
            */

            $returnQty = (int) ($request->return_qty ?? $accessorySale->qty);

            if ($returnQty < 1) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Qty retur tidak valid'
                ]);
            }

            $sisaQty = $accessorySale->qty - ($accessorySale->return_qty ?? 0);

            if ($returnQty > $sisaQty) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Qty retur melebihi qty tersisa'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CEK / BUAT SALES RETURN
            |--------------------------------------------------------------------------
            */

            $sale = $accessorySale->sale;

            $returnInvoice = str_replace(
                'INV',
                'RTR',
                $sale->invoice
            );

            $salesReturn = SalesReturn::where(
                'return_invoice',
                $returnInvoice
            )->first();

            if (!$salesReturn) {

                $salesReturn = SalesReturn::create([

                    'sale_id'        => $sale->id,

                    'return_invoice' => $returnInvoice,

                    'description'    => 'Retur accessories',

                    'user_id'        => Auth::id(),

                    'created_at'     => now(),
                    'total_return'   => 0,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE DETAIL RETURN
            |--------------------------------------------------------------------------
            */

            SalesReturnAccessories::create([

                'sale_return_id'      => $salesReturn->id,

                'accessories_sale_id' => $accessorySale->id,

                'accessories_id'      => $accessorySale->accessories_id,

                'qty'                 => $returnQty,

                'subtotal'            =>
                    $accessorySale->accessories->price * $returnQty,

            ]);
            $salesReturn->increment(
                'total_return',
                $accessorySale->accessories->price * $returnQty
            );

            /*
            |--------------------------------------------------------------------------
            | UPDATE STOCK
            |--------------------------------------------------------------------------
            */

            $accessory = Accessories::find(
                $accessorySale->accessories_id
            );

            $accessory->stok += $returnQty;

            $accessory->save();

            /*
            |--------------------------------------------------------------------------
            | UPDATE RETURN QTY
            |--------------------------------------------------------------------------
            */

            $accessorySale->return_qty =
                ($accessorySale->return_qty ?? 0) + $returnQty;

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS RETURN
            |--------------------------------------------------------------------------
            */

            if ($accessorySale->return_qty >= $accessorySale->qty) {

                $accessorySale->status_return = 1;
            }

            $accessorySale->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Retur accessories berhasil'
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | RETUR ITEM SERIAL
    |--------------------------------------------------------------------------
    */

    public function returnItem(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $itemSale = ItemSale::find($id);

            if (!$itemSale) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Data item sale tidak ditemukan'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CEK SUDAH DIRETUR
            |--------------------------------------------------------------------------
            */

            if ($itemSale->status_return == 1) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Item sudah diretur'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA SALE
            |--------------------------------------------------------------------------
            */

            $sale = Sale::find($itemSale->sale_id);

            if (!$sale) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sale tidak ditemukan'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CEK / BUAT SALES RETURN
            |--------------------------------------------------------------------------
            */

            $returnInvoice = str_replace(
                'INV',
                'RTR',
                $sale->invoice
            );

            $salesReturn = SalesReturn::where(
                'return_invoice',
                $returnInvoice
            )->first();

            if (!$salesReturn) {

                $salesReturn = SalesReturn::create([

                    'sale_id'        => $sale->id,

                    'return_invoice' => $returnInvoice,

                    'user_id'        => Auth::id(),

                    'created_at'     => now(),
                    'total_return'   => 0,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | KEMBALIKAN ITEM KE TABLE ITEMS
            |--------------------------------------------------------------------------
            */

            Item::create([

                'divisi_id'       => $itemSale->divisi_id,

                'itemcategory_id' => $itemSale->itemcategory_id,

                'name'            => $itemSale->name,

                'price'           => $itemSale->price,

                'capital_price'   => $itemSale->capital_price,

                'no_seri'         => $itemSale->no_seri,

                'status'          => 1,

            ]);
            $salesReturn->increment(
                'total_return',
                $itemSale->price
            );
            /*
            |--------------------------------------------------------------------------
            | DETAIL RETURN ITEM
            |--------------------------------------------------------------------------
            */

            SalesReturnItem::create([

                'sale_return_id' => $salesReturn->id,

                'item_sale_id'   => $itemSale->id,

            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS RETURN
            |--------------------------------------------------------------------------
            */

            $itemSale->update([

                'status_return' => 1

            ]);

            DB::commit();

            return response()->json([

                'status' => 'success',

                'message' => 'Item berhasil diretur'

            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'status' => 'error',

                'message' => $e->getMessage()

            ]);
        }
    }   /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            // ===============================
            // 1. BUAT DATA RETUR SALES
            // ===============================
            $invoiceRetur = str_replace('INV', 'INR', $sale->invoice);

            SalesReturn::create([
                'invoice_retur' => $invoiceRetur,
                'sales_id'      => $sale->id,
                'user_id'       => auth()->id(),
                'divisi_id'     => $sale->divisi_id,
            ]);

            // ===============================
            // 2. KEMBALIKAN ITEM SERIAL
            // ===============================
            $itemSales = ItemSale::where('sale_id', $sale->id)->get();

            foreach ($itemSales as $itemSale) {
                Item::create([
                    'divisi_id'        => $itemSale->divisi_id,
                    'itemcategory_id' => $itemSale->itemcategory_id,
                    'name'            => $itemSale->name,
                    'price'           => $itemSale->price,
                    'capital_price'   => $itemSale->capital_price,
                    'no_seri'         => $itemSale->no_seri,
                    'status'          => 1, // tersedia
                ]);

                $itemSale->delete();
            }

            // ===============================
            // 3. KEMBALIKAN STOK ACCESSORIES
            // ===============================
            $accessoriesSales = AccessoriesSale::where('sale_id', $sale->id)->get();

            foreach ($accessoriesSales as $accessorySale) {
                Accessories::where('id', $accessorySale->accessories_id)
                    ->increment('stok', $accessorySale->qty);

                $accessorySale->delete();
            }

            // ===============================
            // 4. HAPUS DEBT
            // ===============================
            Debt::where('sale_id', $sale->id)->delete();

            // ===============================
            // 5. HAPUS SALE
            // ===============================
            $sale->delete(); // soft delete jika pakai SoftDeletes

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan dan dicatat sebagai retur'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
    public function returnFull($id)
    {
        DB::beginTransaction();

        try {

            $sale = Sale::with([
                'itemSales',
                'accessoriesSales'
            ])->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | CEK APAKAH SUDAH DIRETUR
            |--------------------------------------------------------------------------
            */

            $checkReturn = SalesReturn::where('sale_id', $sale->id)->first();

            if ($checkReturn) {

                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah pernah diretur'
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | HITUNG TOTAL RETUR
            |--------------------------------------------------------------------------
            */

            $totalReturn = 0;

            // TOTAL ITEM
            foreach ($sale->itemSales as $itemSale) {

                $totalReturn += $itemSale->price;

            }

            // TOTAL ACCESSORIES
            foreach ($sale->accessoriesSales as $accessorySale) {

                $totalReturn += $accessorySale->subtotal;

            }

            /*
            |--------------------------------------------------------------------------
            | BUAT HEADER RETUR
            |--------------------------------------------------------------------------
            */

            $returnInvoice = str_replace('INV', 'RET', $sale->invoice);

            $salesReturn = SalesReturn::create([

                'sale_id'        => $sale->id,

                'user_id'        => auth()->id(),

                'return_invoice' => $returnInvoice,

                'type'           => 'full',

                'total_return'   => $totalReturn,

                'description'    => 'Retur full transaksi',

            ]);

            /*
            |--------------------------------------------------------------------------
            | RETUR ITEM SERIAL
            |--------------------------------------------------------------------------
            */

            foreach ($sale->itemSales as $itemSale) {

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN KE TABLE ITEMS
                |--------------------------------------------------------------------------
                */

                Item::create([

                    'divisi_id'       => $sale->divisi_id,

                    'itemcategory_id' => $itemSale->itemcategory_id,

                    'name'            => $itemSale->name,

                    'price'           => $itemSale->price,

                    'capital_price'   => $itemSale->capital_price,

                    'no_seri'         => $itemSale->no_seri,

                    'status'          => 1,

                ]);

                /*
                |--------------------------------------------------------------------------
                | SIMPAN DETAIL RETUR ITEM
                |--------------------------------------------------------------------------
                */

                SalesReturnItem::create([

                    'sale_return_id' => $salesReturn->id,

                    'item_sale_id'   => $itemSale->id,

                ]);

                /*
                |--------------------------------------------------------------------------
                | HAPUS ITEM SALE
                |--------------------------------------------------------------------------
                */

                $itemSale->delete();

            }

            /*
            |--------------------------------------------------------------------------
            | RETUR ACCESSORIES
            |--------------------------------------------------------------------------
            */

            foreach ($sale->accessoriesSales as $accessorySale) {

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN STOK
                |--------------------------------------------------------------------------
                */

                Accessories::where('id', $accessorySale->accessories_id)
                    ->increment('stok', $accessorySale->qty);

                /*
                |--------------------------------------------------------------------------
                | SIMPAN DETAIL RETUR ACCESSORIES
                |--------------------------------------------------------------------------
                */

                SalesReturnAccessories::create([

                    'sale_return_id'    => $salesReturn->id,

                    'accessories_sale_id' => $accessorySale->id,

                    'accessories_id'    => $accessorySale->accessories_id,

                    'qty'               => $accessorySale->qty,

                    'subtotal'          => $accessorySale->subtotal,

                ]);

                /*
                |--------------------------------------------------------------------------
                | HAPUS ACCESSORIES SALE
                |--------------------------------------------------------------------------
                */

                $accessorySale->delete();

            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS DEBT
            |--------------------------------------------------------------------------
            */

            Debt::where('sale_id', $sale->id)->delete();

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS RETUR
            |--------------------------------------------------------------------------
            */

            $sale->update([

                'status_return' => 1

            ]);

            DB::commit();

            return response()->json([

                'success' => true,

                'message' => 'Full transaksi berhasil diretur'

            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);

        }
    }
    public function salesReturn(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $divisiId = $request->divisi_id;

        $query = SalesReturn::with([
            'sale.customer',
            'sale.divisi',
            'user'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        if ($year != 'all') {

            $query->whereYear('created_at', $year);

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if ($month != 'all') {

            $query->whereMonth('created_at', $month);

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DIVISI
        |--------------------------------------------------------------------------
        */

        if (!empty($divisiId) && $divisiId != 'all') {

            $query->whereHas('sale', function ($q) use ($divisiId) {

                $q->where('divisi_id', $divisiId);

            });

        }

        $salesReturns = $query
            ->with([
                'returnItems.itemSale',
                'returnAccessories.accessories',
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA FILTER
        |--------------------------------------------------------------------------
        */

        $years = SalesReturn::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $divisi = Divisi::all();

        return view('manager.sale.return-index', compact(
            'salesReturns',
            'years',
            'divisi'
        ));
    }
}
