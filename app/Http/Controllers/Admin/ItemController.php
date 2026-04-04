<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemIn;
use App\Models\ItemSale;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itemsByCategory = Item::with('cat', 'divisi')
            ->select('itemcategory_id',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();
        $item = Item::all()->count();
        return view('admin.item.index', compact('itemsByCategory', 'item'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cat = ItemCategory::findOrFail($id);
        $item = $cat->item;
        return view('admin.item.show', compact('cat', 'item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Item::whereId($id)->first();
        $cat = ItemCategory::all();
        return view('admin.editItem.edit', compact('item', 'cat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // Bersihkan format rupiah (WAJIB)
        $price = str_replace('.', '', $request->price);
        $request->merge([
            'price' => $price
        ]);

        // Ambil price_bottom dari data lama
        $priceBottom = $item->price_bottom ?? 0;

        $request->validate([
            'name' => 'required',
            'itemcategory_id' => 'required',
            'no_seri' => 'required|unique:items,no_seri,' . $id,
            'price' => 'required|numeric|min:' . $priceBottom,
        ], [
            'name.required' => 'Nama Tidak Boleh Kosong',
            'itemcategory_id.required' => 'Pilih Category',
            'no_seri.required' => 'Nomor Seri Tidak Boleh Kosong',
            'no_seri.unique' => 'Nomor Seri Sudah Terdaftar',
            'price.required' => 'Price tidak boleh kosong',
            'price.numeric' => 'Price harus berupa angka',
            'price.min' => 'Price tidak boleh lebih kecil dari price bottom (' . number_format($priceBottom) . ')',
        ]);

        // Update data item
        $item->update([
            'itemcategory_id' => $request->itemcategory_id,
            'name' => $request->name,
            'no_seri' => $request->no_seri,
            'created_at' => $request->created_at,
            'price' => $request->price,
        ]);

        // Update / create item_ins
        ItemIn::updateOrCreate(
            ['no_seri' => $item->no_seri],
            [
                'itemcategory_id' => $item->itemcategory_id,
                'name' => $item->name,
                'price' => $item->price,
                'created_at' => $item->created_at,
                'kode_msk' => $request->kode_msk,
            ]
        );

        // Handle pembelian
        $invoice = $request->kode_msk;
        $divisiId = Auth::user()->divisi_id;

        $existingPembelian = Pembelian::where('invoice', $invoice)
            ->where('divisi_id', $divisiId)
            ->first();

        if (!$existingPembelian && $invoice) {
            Pembelian::create([
                'divisi_id' => $divisiId,
                'invoice' => $invoice,
                'status' => '1',
            ]);
        }

        return redirect()->route('admin.item.editItem')
            ->withSuccess('Data berhasil disimpan');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sale()
    {
        $sale = ItemSale::whereHas('sale.divisi', function ($query){
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
            ->with('sale', 'itemCategory')->get();
        return view('admin.item.sale', compact('sale'));
    }

    public function EditItem(){
        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $items = Item::where('divisi_id', Auth::user()->divisi_id)->get();

        return view('admin.editItem.index', compact('items'));
    }
}
