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
        $sale = Sale::with(['customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])->get();
        return view('manager.sale.index', compact('sale'));
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
                            'subtotal' => $accessory['subtotal']
                        ]);
                    }
                }
            }

            // Save Item sale and remove from items
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
                'message' => 'sale saved successfully.'
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
        $sale = Sale::with(['itemSales', 'accessoriesSales.accessories'])->findOrFail($id);
        $customers = Customer::all();

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
        // Dapatkan data penjualan yang ada
        $sale = Sale::findOrFail($id);

        // Data item dan aksesori yang saat ini terkait dengan penjualan
        $currentItems = $sale->itemSales;
        $currentAccessories = $sale->accessoriesSales;

        // Dapatkan item dan aksesori baru dari request
        $newItems = $request->input('items', []); // array of item ids
        $newAccessories = $request->input('accessories', []); // array of accessories ids with quantities

        // Hapus item yang tidak ada di data baru dan kembalikan ke stok
        foreach ($currentItems as $currentItem) {
            if (!in_array($currentItem->item_id, $newItems)) {
                // Kembalikan stok item ke tabel items
                $item = Item::findOrFail($currentItem->item_id);
                $item->status = 1; // Atur status item kembali ke 1 (tersedia)
                $item->save();

                // Hapus dari tabel item_sales
                $currentItem->delete();
            }
        }

        // Hapus aksesori yang tidak ada di data baru dan kembalikan ke stok
        foreach ($currentAccessories as $currentAccessory) {
            $found = false;
            foreach ($newAccessories as $newAccessory) {
                if ($currentAccessory->accessories_id == $newAccessory['id']) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Kembalikan stok aksesori ke tabel accessories
                $accessory = Accessories::findOrFail($currentAccessory->accessories_id);
                $accessory->stok += $currentAccessory->qty;
                $accessory->save();

                // Hapus dari tabel accessories_sales
                $currentAccessory->delete();
            }
        }

        // Tambah atau perbarui item baru di penjualan
        foreach ($newItems as $itemId) {
            // Periksa apakah item sudah ada di penjualan
            if (!$sale->itemSales->where('item_id', $itemId)->exists()) {
                $sale->itemSales()->create(['item_id' => $itemId]);

                // Set item status ke 2 (dipesan) jika perlu
                $item = Item::findOrFail($itemId);
                $item->status = 2; // Atur status item ke 2 (dipesan)
                $item->save();
            }
        }

        // Tambah atau perbarui aksesori baru di penjualan
        foreach ($newAccessories as $newAccessory) {
            $accessoryId = $newAccessory['id'];
            $quantity = $newAccessory['qty'];

            // Periksa apakah aksesori sudah ada di penjualan
            $existingAccessory = $sale->accessoriesSales->where('accessories_id', $accessoryId)->first();
            if ($existingAccessory) {
                // Perbarui kuantitas aksesori jika sudah ada
                $existingAccessory->qty = $quantity;
                $existingAccessory->subtotal = $quantity * $existingAccessory->accessory->price; // Misalkan harga aksesori bisa didapat dari relasi
                $existingAccessory->save();
            } else {
                // Tambah aksesori baru ke penjualan
                $sale->accessoriesSales()->create([
                    'accessories_id' => $accessoryId,
                    'qty' => $quantity,
                    'subtotal' => $quantity * Accessories::findOrFail($accessoryId)->price,
                ]);
            }
        }

        // Simpan perubahan lainnya ke model Sale
        $sale->update($request->except(['items', 'accessories'])); // Update field selain items dan accessories

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
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
