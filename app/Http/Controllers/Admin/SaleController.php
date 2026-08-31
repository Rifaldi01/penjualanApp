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
        $year = $request->year ?? date('Y');

        // Semua transaksi aktif (semua tahun)
        $salesActive = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->with([
                'customer.divisi',
                'user',
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
            ->where('status_return', 0)
            ->get();

        // Query transaksi lunas
        $salesQuery = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->with([
                'customer.divisi',
                'user',
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
            ]);

        // Jika bukan ALL maka filter tahun
        if ($year != 'all') {
            $salesQuery->whereYear('created_at', $year);
        }

        $sales = $salesQuery->latest()->get();

        // Ambil daftar tahun
        $years = Sale::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $bank = Bank::all();

        return view('admin.sale.index', compact(
            'sales',
            'salesActive',
            'bank',
            'years',
            'year'
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
        $divisiId = Auth::user()->divisi_id;

        $accessories = Accessories::where('divisi_id', $divisiId)
            ->where('stok', '>', 0)
            ->get();

        $items = Item::where('divisi_id', $divisiId)
            ->where('status', 0)
            ->get();

        $customer = Customer::where('divisi_id', $divisiId)->get();

        $bank = Bank::all();

        return view('admin.sale.create', compact(
            'accessories',
            'items',
            'customer',
            'bank'
        ));
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
        $currentMonthNumber = str_pad(date('n'), 2, '0', STR_PAD_LEFT);
        $invFormat = $divisi->inv_format;

        $lastInvoice = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->whereYear('created_at', $currentYear)
            ->where('invoice', 'like', "INV/{$invFormat}/%/%/{$currentYear}")
            ->orderByDesc('id')
            ->first();

        if ($lastInvoice) {
            preg_match(
                '/INV\/' . preg_quote($invFormat, '/') . '\/(\d{4})\/\d{2}\/' . $currentYear . '/',
                $lastInvoice->invoice,
                $matches
            );
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
        } else {
            $lastNumber = 0;
        }

        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $invoiceNumber = "INV/{$invFormat}/{$nextNumber}/{$currentMonthNumber}/{$currentYear}";

        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'total_item' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'ongkir' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'accessories' => 'nullable|array',
            'items' => 'nullable|array'
        ], [
            'customer_id.required' => 'Pelanggan wajib diisi.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak valid.',
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
        ]);

        DB::beginTransaction();

        try {
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
                'created_at' => $request->created_at ?? now(),
                'no_po' => $request->no_po,
                'inv_manual' => $request->inv_manual,
                'admin_fee' => $request->admin_fee,
                'user_id' => Auth::id(),
                'divisi_id' => Auth::user()->divisi_id,
                'invoice' => $invoiceNumber
            ]);

            $nominalIn = (int)str_replace('.', '', $sale->nominal_in);

            if ($nominalIn > 0) {
                Debt::create([
                    'sale_id' => $sale->id,
                    'pay_debts' => $sale->nominal_in,
                    'bank_id' => $request->bank_id,
                    'penerima' => $request->penerima,
                    'description' => $request->description,
                    'date_pay' => $request->date_pay ?? now()
                ]);
            }

            // ACCESSORIES
            if ($request->has('accessories') && is_array($validated['accessories'] ?? null)) {
                foreach ($validated['accessories'] as $accessory) {
                    $accessoryRecord = Accessories::find($accessory['accessories_id']);

                    if (!$accessoryRecord) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Accessories dengan ID ' . $accessory['accessories_id'] . ' tidak ditemukan.'
                        ], 400);
                    }

                    $priceSale = (float)($accessory['price_sale'] ?? 0);
                    $priceBottom = (float)($accessoryRecord->price_bottom ?? 0);

                    if ($priceSale < $priceBottom) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Harga jual accessories "' . $accessoryRecord->name . '" tidak boleh lebih kecil dari harga minimum Rp ' . number_format($priceBottom, 0, ',', '.')
                        ], 422);
                    }

                    $qty = (float)($accessory['qty'] ?? 0);

                    if ($qty <= 0) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Qty accessories "' . $accessoryRecord->name . '" harus lebih dari 0.'
                        ], 422);
                    }

                    if ($qty > (float)$accessoryRecord->stok) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Stok ' . $accessoryRecord->name . ' Tidak Mencukupi. Stok Tersedia: ' . $accessoryRecord->stok
                        ], 400);
                    }

                    $accessoryRecord->stok -= $qty;
                    $accessoryRecord->save();

                    AccessoriesSale::create([
                        'sale_id' => $sale->id,
                        'accessories_id' => $accessoryRecord->id,
                        'qty' => $qty,
                        'price_sale' => $priceSale,
                        'subtotal' => $accessory['subtotal'] ?? 0,
                        'acces_out' => $request->created_at ?? now()
                    ]);
                }
            }

            // ITEMS / ALAT
            if ($request->has('items') && is_array($validated['items'] ?? null)) {
                foreach ($validated['items'] as $item) {
                    $itemRecord = Item::where('itemcategory_id', $item['itemcategory_id'])
                        ->where('no_seri', $item['no_seri'])
                        ->first();

                    if (!$itemRecord) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Alat dengan nomor seri "' . $item['no_seri'] . '" tidak ditemukan.'
                        ], 400);
                    }

                    $priceSale = (float)($item['price'] ?? 0);
                    $priceBottom = (float)($itemRecord->price_bottom ?? 0);

                    if ($priceSale < $priceBottom) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Harga jual alat "' . ($itemRecord->name ?? $item['name']) . '" tidak boleh lebih kecil dari harga minimum Rp ' . number_format($priceBottom, 0, ',', '.')
                        ], 422);
                    }

                    ItemSale::create([
                        'sale_id' => $sale->id,
                        'itemcategory_id' => $item['itemcategory_id'],
                        'region' => $itemRecord->region,
                        'name' => $item['name'],
                        'no_seri' => $item['no_seri'],
                        'price' => $priceSale,
                        'divisi_id' => $sale->divisi_id,
                        'capital_price' => $itemRecord->capital_price,
                        'date_in' => $itemRecord->created_at
                    ]);

                    $itemRecord->delete();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sale berhasil disimpan.',
                'invoice' => $invoiceNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('SALE STORE ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
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
        //
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
            'nominal_in' => 'required',
            'date_pay' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {

            $nominal_in = (int)str_replace(['Rp', '.', ',', ' '], '', $request->nominal_in);
            $pay_debts = (int)str_replace(['Rp', '.', ',', ' '], '', $request->pay_debts ?? 0);
            $admin_fee = (int)str_replace(['Rp', '.', ',', ' '], '', $request->admin_fee ?? 0);
            $diskon = (int)str_replace(['Rp', '.', ',', ' '], '', $request->diskon ?? 0);
            $fee = (int)str_replace(['Rp', '.', ',', ' '], '', $request->fee ?? 0);

            $sale = Sale::findOrFail($id);

            $admin_fee_lama = (int)$sale->admin_fee;
            $diskon_lama = (int)$sale->diskon;
            $fee_lama = (int)$sale->fee;

            /*
            |--------------------------------------------------------------------------
            | Hitung selisih biaya
            |--------------------------------------------------------------------------
            */

            $selisih_admin = $admin_fee - $admin_fee_lama;
            $selisih_diskon = $diskon - $diskon_lama;
            $selisih_fee = $fee - $fee_lama;

            /*
            |--------------------------------------------------------------------------
            | Update pay
            |--------------------------------------------------------------------------
            */

            $sale->pay =
                $sale->pay
                - $selisih_admin
                - $selisih_diskon;
            /*
            |--------------------------------------------------------------------------
            | Update sale
            |--------------------------------------------------------------------------
            */

            $sale->nominal_in = $nominal_in;
            $sale->admin_fee = $admin_fee;
            $sale->diskon = $diskon;
            $sale->fee = $fee;

            $sale->save();

            /*
            |--------------------------------------------------------------------------
            | Buat Debt hanya jika ada pembayaran
            |--------------------------------------------------------------------------
            */

            if ($pay_debts > 0) {

                // Validasi bank / lainnya
                if (empty($request->bank_id) && empty($request->description)) {
                    DB::rollBack();

                    return back()->withErrors([
                        'bank_id' => 'Kolom Bank atau Lainnya harus diisi.',
                    ])->withInput();
                }

                // Validasi penerima
                if (!empty($request->bank_id) && empty($request->penerima)) {
                    DB::rollBack();

                    return back()->withErrors([
                        'penerima' => 'Masukkan Nama Penerima.',
                    ])->withInput();
                }

                Debt::create([
                    'sale_id' => $sale->id,
                    'bank_id' => $request->bank_id,
                    'pay_debts' => $pay_debts,
                    'date_pay' => $request->date_pay,
                    'penerima' => $request->penerima,
                    'description' => $request->description,
                ]);
            }

            DB::commit();

            return back()->withSuccess('Data berhasil diperbarui');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage()
            ])->withInput();
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

                'sale_id' => $sale->id,

                'user_id' => auth()->id(),

                'return_invoice' => $returnInvoice,

                'type' => 'full',

                'total_return' => $totalReturn,

                'description' => 'Retur full transaksi',

                'created_at' => now(),

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

                    'divisi_id' => $sale->divisi_id,

                    'itemcategory_id' => $itemSale->itemcategory_id,

                    'name' => $itemSale->name,

                    'price' => $itemSale->price,

                    'capital_price' => $itemSale->capital_price,

                    'no_seri' => $itemSale->no_seri,

                    'status' => 1,

                ]);

                SalesReturnItem::create([

                    'sale_return_id' => $salesReturn->id,

                    'item_sale_id' => $itemSale->id,

                    'created_at' => $sale->created_at,

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

                    'sale_return_id' => $salesReturn->id,

                    'accessories_sale_id' => $accessorySale->id,

                    'accessories_id' => $accessorySale->accessories_id,

                    'qty' => $accessorySale->qty,

                    'subtotal' => $accessorySale->subtotal,

                    'created_at' => $sale->created_at,

                ]);

                $accessorySale->update([

                    'return_qty' => $accessorySale->qty,

                    'status_return' => 1,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | HAPUS PIUTANG
            |--------------------------------------------------------------------------
            */
            $sale->delete();
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
    public function updateFee(Request $request, $id)
    {
        $request->validate([
            'fee' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        try {

            DB::beginTransaction();

            $sale = Sale::findOrFail($id);

            $sale->fee = $request->fee;
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fee berhasil diperbarui.',
                'data' => [
                    'id' => $sale->id,
                    'invoice' => $sale->invoice,
                    'fee' => $sale->fee,
                ],
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui fee.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
