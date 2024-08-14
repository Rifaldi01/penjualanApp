<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccesoriesCategory;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\CategoryItem;
use App\Models\Customer;
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
        $sale = Sale::with(['customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();
        return view('admin.sale.index', compact('sale'));
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
            // Create Sale record
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'total_item' => $request->total_item,
                'total_price' => $request->total_price,
                'ongkir' => $request->ongkir,
                'diskon' => $request->diskon,
                'pay' => $request->bayar,
                'user_id' => auth()->id()
            ]);

            // Save Accessories Sale and update stock
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

                        // Save to Accessories Sale
                        AccessoriesSale::create([
                            'sale_id' => $sale->id,
                            'accessories_id' => $accessory['accessories_id'],
                            'qty' => $qty,
                            'subtotal' => $accessory['subtotal']
                        ]);
                    }
                }
            }

            // Save Item Sale and remove from items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    // Save to item_sale
                    ItemSale::create([
                        'sale_id' => $sale->id,
                        'itemcategory_id' => $item['itemcategory_id'],
                        'name' => $item['name'],
                        'no_seri' => $item['no_seri'],
                        'price' => $item['price']
                    ]);

                    // Remove item from items
                    Item::where('itemcategory_id', $item['itemcategory_id'])
                        ->where('no_seri', $item['no_seri'])
                        ->delete();
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
        //
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
}
