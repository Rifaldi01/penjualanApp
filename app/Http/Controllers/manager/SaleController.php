<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sales = Sale::with(['customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();

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

        // Pass data ke view
        return view('manager.sale.index', compact('sales'));
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
        return view('manager.sale.create', compact('accessories', 'item', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'total_item' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'ongkir' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'accessories' => 'array',
            'items' => 'array'
        ]);

        DB::beginTransaction();

        try {
            // Create sale record
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'total_item' => $request->total_item,
                'total_price' => $request->total_price,
                'ongkir' => $request->ongkir,
                'diskon' => $request->diskon,
                'pay' => $request->bayar,
                'nominal_in' => $request->nominal_in,
                'deadlines' => $request->deadlines,
                'user_id' => auth()->id()
            ]);

            // Save Accessories sale and update stock
            if ($request->has('accessories')) {
                foreach ($request->accessories as $accessory) {
                    $accessoryRecord = Accessories::find($accessory['accessories_id']);

                    if ($accessoryRecord) {
                        $currentStock = $accessoryRecord->stok;
                        $qty = $accessory['qty'];

                        if ($qty > $currentStock) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Stok kurang untuk aksesori dengan ID ' . $accessory['accessories_id']
                            ], 400);
                        }

                        // Update stock
                        $accessoryRecord->stok -= $qty;
                        $accessoryRecord->save();

                        // Save to Accessories sale
                        AccessoriesSale::create([
                            'sale_id' => $sale->id,
                            'accessories_id' => $accessory['accessories_id'],
                            'qty' => $qty,
                            'subtotal' => $accessory['subtotal'],
                            'acces_out' => now()
                        ]);
                    }
                }
            }

            // Save Item sale and remove from items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $itemRecord = Item::where('itemcategory_id', $item['itemcategory_id'])
                        ->where('no_seri', $item['no_seri'])
                        ->first();

                    if ($itemRecord) {
                        // Save to item_sale
                        ItemSale::create([
                            'sale_id' => $sale->id,
                            'itemcategory_id' => $item['itemcategory_id'],
                            'name' => $item['name'],
                            'no_seri' => $item['no_seri'],
                            'price' => $item['price'],
                            'date_in' => $itemRecord->created_at // Use created_at from the items table
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
                'message' => 'An error occurred while saving the sale. Please try again.'
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
            $sale = Sale::with(['itemSales', 'accessoriesSales.accessories'])->findOrFail($id);
            return response()->json($sale);
        }
        $customers = Customer::all();
        $sale = Sale::with(['itemSales', 'accessoriesSales.accessories'])->findOrFail($id);
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

            //eta no_seri teh naon weuh di request. salah a kuduna items etamana nama colom dina item_sales
            //ke ieu dibenerken cobaan hela edit deui save hela?nya
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $itemSale = new ItemSale();
                    $itemSale->sale_id = $sale->id;
                    $itemSale->name = $item['name'];//item_name weuh di request oh name hungkul soalna ieu teh mindahkeun ti item ka item_name. Enya  pokokna $requiewst->item_name pasti error soalna null. weuh di jero request
                    $itemSale->no_seri = $item['no_seri']; //no seri ieu teh di jero item_sales? enya
                    $itemSale->price = str_replace('.', '', $item['price']);
                    $itemSale->itemcategory_id = $item['itemcategory_id'];
                    $itemSale->date_in = now();
                    $itemSale->save();
                }
            }

            //ieu naon accecories_id? data nu dikirimna accessories
            //data request anu dikirim kadieu teh ieu
            //euweuh data accessories_id, ayana data accessories. jadi if request->hgas('accessories_id') moal kapanggil

            //kitu cara manggilna. bieu mah salah save tos
            if ($request->has('accessories')) {
                foreach ($request->accessories as $item) {
                    $id = $item['id'] ?? null;
                    $accessoriesSale = AccessoriesSale::firstOrNew(['id' => $id]);
                    $accessoriesSale->sale_id = $item['sale_id'];
                    $accessoriesSale->accessories_id = $item['accessories_id'];
                    $accessoriesSale->qty = $item['qty'];
                    $accessoriesSale->subtotal = $item['subtotal'];//$request->qty[$key] * str_replace('.', '', $request->price[$key]);
                    $accessoriesSale->acces_out= now();
                    $accessoriesSale->save();
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
        //
    }

    public function fetchData(Request $request)
    {
        $codeSale = $request->get('code');
        $accessory = Accessories::where('code_acces', $codeSale)->first();
        $item = Item::where('no_seri', $codeSale)->first();

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
        $sale = Sale::findOrFail($id);
        $sale->nominal_in = $request->input('nominal_in');
        $sale->save();
        return back()->withSuccess('Pembayaran Lunas');
    }
}
