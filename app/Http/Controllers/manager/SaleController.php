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
        $salesactive = Sale::with(['itemSales' => function ($query) {
            $query->where('status_return', 0);
        },
            'itemSales.itemCategory',
            'accessoriesSales' => function ($query) {
                $query->where('status_return', 0);
            },
            'accessoriesSales.accessories',
            'divisi',
            'debt.bank'])->get();

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
            | TANGGAL INVOICE ACUAN
            |--------------------------------------------------------------------------
            */

            $tanggalInvoice = $sale->fresh()->created_at;

            /*
            |--------------------------------------------------------------------------
            | SYNC TANGGAL DETAIL LAMA
            |--------------------------------------------------------------------------
            */

            AccessoriesSale::where('sale_id', $sale->id)
                ->update([
                    'acces_out'  => $tanggalInvoice,
                    'created_at' => $tanggalInvoice,
                ]);

            ItemSale::where('sale_id', $sale->id)
                ->update([
                    'created_at' => $tanggalInvoice,
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
                    if ($row['status'] == 'deleted') {

                        $detail = AccessoriesSale::find($row['sale_detail_id']);

                        if (!$detail) {
                            continue;
                        }

                        $accessory = Accessories::find($detail->accessories_id);

                        $returnQty = $detail->qty;

                        $salesReturn = SalesReturn::firstOrCreate(

                            [
                                'return_invoice' => str_replace('INV','RTR',$sale->invoice)
                            ],

                            [
                                'sale_id'=>$sale->id,
                                'user_id'=>Auth::id(),
                                'created_at'=>now(),
                                'description'=>'Retur Barang',
                                'total_return'=>0
                            ]

                        );

                        SalesReturnAccessories::create([

                            'sale_return_id'=>$salesReturn->id,

                            'accessories_sale_id'=>$detail->id,

                            'accessories_id'=>$detail->accessories_id,

                            'qty'=>$returnQty,

                            'subtotal'=>$returnQty * $accessory->price

                        ]);

                        $salesReturn->increment(

                            'total_return',

                            $returnQty * $accessory->price

                        );

                        $accessory->increment('stok',$returnQty);

                        $detail->update([

                            'return_qty'=>$detail->qty,

                            'status_return'=>1

                        ]);

                        continue;

                    }
                    if ($row['status'] == 'new') {

                        $accessory = Accessories::find($row['accessories_id']);

                        if (!$accessory) {
                            continue;
                        }

                        if ($accessory->stok < $row['qty']) {

                            DB::rollBack();

                            return response()->json([
                                'status'  => 'error',
                                'message' => 'Stok accessories '.$accessory->name.' tidak mencukupi.'
                            ]);
                        }

                        AccessoriesSale::create([

                            'sale_id'        => $sale->id,
                            'accessories_id' => $accessory->id,
                            'qty'            => $row['qty'],
                            'subtotal'       => $accessory->price * $row['qty'],
                            'acces_out'      => $tanggalInvoice,

                            'return_qty'     => 0,
                            'status_return'  => 0,

                            'created_at'     => $tanggalInvoice,
                            'updated_at'     => now(),

                        ]);

                        $accessory->decrement('stok', $row['qty']);

                        continue;
                    }

                    if ($row['status'] == 'old') {

                        $detail = AccessoriesSale::find($row['sale_detail_id']);

                        if (!$detail) {
                            continue;
                        }

                        $accessory = Accessories::find($detail->accessories_id);

                        $selisih = $row['qty'] - $detail->qty;

                        // Qty bertambah
                        if ($selisih > 0) {

                            if ($accessory->stok < $selisih) {

                                DB::rollBack();

                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'Stok accessories tidak cukup'
                                ]);
                            }

                            $accessory->decrement('stok', $selisih);
                        }

                        // Qty berkurang
                        if ($selisih < 0) {

                            $returnQty = abs($selisih);

                            /*
                            |--------------------------------------------------------------------------
                            | SALES RETURN
                            |--------------------------------------------------------------------------
                            */

                            $salesReturn = SalesReturn::firstOrCreate(
                                [
                                    'return_invoice' => str_replace('INV', 'RTR', $sale->invoice)
                                ],
                                [
                                    'sale_id'      => $sale->id,
                                    'user_id'      => Auth::id(),
                                    'created_at'   => now(),
                                    'description'  => 'Retur Barang',
                                    'total_return' => 0,
                                ]
                            );

                            SalesReturnAccessories::create([
                                'sale_return_id'      => $salesReturn->id,
                                'accessories_sale_id' => $detail->id,
                                'accessories_id'      => $detail->accessories_id,
                                'qty'                 => $returnQty,
                                'subtotal'            => $accessory->price * $returnQty,
                            ]);

                            $salesReturn->increment(
                                'total_return',
                                $accessory->price * $returnQty
                            );

                            $detail->return_qty += $returnQty;

                            if ($detail->return_qty >= $detail->qty) {
                                $detail->status_return = 1;
                            }

                            $detail->save();

                            // Kembalikan stok
                            $accessory->increment('stok', $returnQty);
                        }

                        $detail->update([
                            'qty'        => $row['qty'],
                            'subtotal'   => $accessory->price * $row['qty'],
                            'acces_out'  => $tanggalInvoice,
                            'created_at' => $tanggalInvoice,
                        ]);
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

                            'status_return'   => 0,

                            'created_at'      => $tanggalInvoice,

                            'updated_at'      => now(),

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
                    if ($row['status'] == 'deleted') {

                        $detail = ItemSale::find($row['sale_detail_id']);

                        if(!$detail){
                            continue;
                        }

                        Item::create([

                            'divisi_id'=>$sale->divisi_id,

                            'itemcategory_id'=>$detail->itemcategory_id,

                            'name'=>$detail->name,

                            'price'=>$detail->price,

                            'capital_price'=>$detail->capital_price,

                            'no_seri'=>$detail->no_seri,

                            'status'=>1

                        ]);

                        $salesReturn = SalesReturn::firstOrCreate(

                            [
                                'return_invoice'=>str_replace('INV','RTR',$sale->invoice)
                            ],

                            [
                                'sale_id'=>$sale->id,
                                'user_id'=>Auth::id(),
                                'created_at'=>now(),
                                'description'=>'Retur Barang',
                                'total_return'=>0
                            ]

                        );

                        SalesReturnItem::create([

                            'sale_return_id'=>$salesReturn->id,

                            'item_sale_id'=>$detail->id

                        ]);

                        $salesReturn->increment(

                            'total_return',

                            $detail->price

                        );

                        $detail->update([

                            'status_return'=>1

                        ]);

                        continue;

                    }

                    if ($row['status'] == 'old') {

                        $detail = ItemSale::find(
                            $row['sale_detail_id']
                        );

                        if (!$detail) {
                            continue;
                        }

                        $detail->update([

                            'price' => $row['price'],

                            'created_at' => $tanggalInvoice,

                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SYNC ULANG SETELAH SEMUA PROSES
            |--------------------------------------------------------------------------
            */

            AccessoriesSale::where('sale_id', $sale->id)
                ->update([
                    'acces_out'  => $tanggalInvoice,
                    'created_at' => $tanggalInvoice,
                ]);

            ItemSale::where('sale_id', $sale->id)
                ->update([
                    'created_at' => $tanggalInvoice,
                ]);

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

    /**
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
            'nominal_in' => 'required',
            'date_pay'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {

            $nominal_in = (int) str_replace(['Rp', '.', ',', ' '], '', $request->nominal_in);
            $pay_debts  = (int) str_replace(['Rp', '.', ',', ' '], '', $request->pay_debts ?? 0);
            $admin_fee  = (int) str_replace(['Rp', '.', ',', ' '], '', $request->admin_fee ?? 0);
            $diskon     = (int) str_replace(['Rp', '.', ',', ' '], '', $request->diskon ?? 0);
            $fee        = (int) str_replace(['Rp', '.', ',', ' '], '', $request->fee ?? 0);

            $sale = Sale::findOrFail($id);

            $admin_fee_lama = (int) $sale->admin_fee;
            $diskon_lama    = (int) $sale->diskon;
            $fee_lama       = (int) $sale->fee;

            /*
            |--------------------------------------------------------------------------
            | Hitung selisih biaya
            |--------------------------------------------------------------------------
            */

            $selisih_admin  = $admin_fee - $admin_fee_lama;
            $selisih_diskon = $diskon - $diskon_lama;
            $selisih_fee    = $fee - $fee_lama;

            /*
            |--------------------------------------------------------------------------
            | Update pay
            |--------------------------------------------------------------------------
            */

            $sale->pay =
                $sale->pay
                - $selisih_admin
                - $selisih_diskon
                - $selisih_fee;

            /*
            |--------------------------------------------------------------------------
            | Jika ada pembayaran hutang
            |--------------------------------------------------------------------------
            */

            if ($pay_debts > 0) {
                $sale->pay -= $pay_debts;
            }

            /*
            |--------------------------------------------------------------------------
            | Update sale
            |--------------------------------------------------------------------------
            */

            $sale->nominal_in = $nominal_in;
            $sale->admin_fee  = $admin_fee;
            $sale->diskon     = $diskon;
            $sale->fee        = $fee;

            $sale->save();

            /*
            |--------------------------------------------------------------------------
            | Simpan debt hanya jika ada pembayaran
            |--------------------------------------------------------------------------
            */

            if ($pay_debts > 0) {

                if (empty($request->bank_id) && empty($request->description)) {
                    DB::rollBack();

                    return back()->withErrors([
                        'bank_id' => 'Kolom Bank atau Lainnya harus diisi.',
                    ])->withInput();
                }

                if (!empty($request->bank_id) && empty($request->penerima)) {
                    DB::rollBack();

                    return back()->withErrors([
                        'penerima' => 'Masukkan Nama Penerima.',
                    ])->withInput();
                }

                Debt::create([
                    'sale_id'     => $sale->id,
                    'bank_id'     => $request->bank_id,
                    'pay_debts'   => $pay_debts,
                    'date_pay'    => $request->date_pay,
                    'penerima'    => $request->penerima,
                    'description' => $request->description,
                ]);
            }

            DB::commit();

            return back()->withSuccess('Pembayaran Berhasil');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage()
            ])->withInput();
        }
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
            | CEK SUDAH DIRETUR
            |--------------------------------------------------------------------------
            */

            $checkReturn = SalesReturn::where(
                'sale_id',
                $sale->id
            )->first();

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

            foreach ($sale->itemSales as $itemSale) {

                if ($itemSale->status_return == 0) {
                    $totalReturn += $itemSale->price;
                }
            }

            foreach ($sale->accessoriesSales as $accessorySale) {

                if ($accessorySale->status_return == 0) {
                    $totalReturn += $accessorySale->subtotal;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HEADER RETURN
            |--------------------------------------------------------------------------
            */

            $returnInvoice = str_replace(
                'INV',
                'RTR',
                $sale->invoice
            );

            $salesReturn = SalesReturn::create([

                'sale_id'        => $sale->id,

                'user_id'        => auth()->id(),

                'return_invoice' => $returnInvoice,

                'type'           => 'full',

                'total_return'   => $totalReturn,

                'description'    => 'Retur full transaksi',

                'created_at'     => now(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | RETURN ITEM
            |--------------------------------------------------------------------------
            */

            foreach ($sale->itemSales as $itemSale) {

                if ($itemSale->status_return == 1) {
                    continue;
                }

                Item::create([

                    'divisi_id'       => $sale->divisi_id,

                    'itemcategory_id' => $itemSale->itemcategory_id,

                    'name'            => $itemSale->name,

                    'price'           => $itemSale->price,

                    'capital_price'   => $itemSale->capital_price,

                    'no_seri'         => $itemSale->no_seri,

                    'status'          => 1,

                ]);

                SalesReturnItem::create([

                    'sale_return_id' => $salesReturn->id,

                    'item_sale_id'   => $itemSale->id,

                    'created_at'     => $sale->created_at,

                ]);

                $itemSale->update([

                    'status_return' => 1

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | RETURN ACCESSORIES
            |--------------------------------------------------------------------------
            */

            foreach ($sale->accessoriesSales as $accessorySale) {

                if ($accessorySale->status_return == 1) {
                    continue;
                }

                Accessories::where(
                    'id',
                    $accessorySale->accessories_id
                )->increment(
                    'stok',
                    $accessorySale->qty
                );

                SalesReturnAccessories::create([

                    'sale_return_id'      => $salesReturn->id,

                    'accessories_sale_id' => $accessorySale->id,

                    'accessories_id'      => $accessorySale->accessories_id,

                    'qty'                 => $accessorySale->qty,

                    'subtotal'            => $accessorySale->subtotal,

                    'created_at'          => $sale->created_at,

                ]);

                $accessorySale->update([

                    'return_qty'    => $accessorySale->qty,

                    'status_return' => 1,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS PIUTANG
            |--------------------------------------------------------------------------
            */

            Debt::where(
                'sale_id',
                $sale->id
            )->delete();

            /*
            |--------------------------------------------------------------------------
            | UPDATE SALE
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
