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
        $validate = $request->validate([
            'name' => 'required',
            'itemcategory_id' => 'required',
            'no_seri' => $id ? 'required' : 'required|unique:items',
        ], [
            'name.required' => 'Nama Tidak Boleh Kosong',
            'itemcategory_id.required' => 'Pilih Category',
            'no_seri.required' => 'Nomor Seri Tidak Boleh Kosong',
            'no_seri.unique' => 'Nomor Seri Sudah Terdaftar',
        ]);

        // Simpan atau update data di tabel `items`
        $item = Item::updateOrCreate(
            ['id' => $id],
            [
                'itemcategory_id' => $request->input('itemcategory_id'),
                'name' => $request->input('name'),
                'no_seri' => $request->input('no_seri'),
                'created_at' => $request->input('created_at'),
                'price' => $request->input('price'),
                'capital_price' => $request->input('capital_price'),
            ]
        );

        // Update atau buat data di tabel `item_ins`
        ItemIn::updateOrCreate(
            ['no_seri' => $item->no_seri],
            [
                'itemcategory_id' => $item->itemcategory_id,
                'name' => $item->name,
                'price' => $item->price,
                'capital_price' => $item->capital_price,
                'created_at' => $item->created_at,
                'kode_msk' => $request->input('kode_msk'),
            ]
        );

        // Ambil invoice dari request
        $invoice = $request->input('kode_msk');
        $divisiId = Auth::user()->divisi_id;

        // Cek apakah invoice dengan divisi yang sama sudah ada
        $existingPembelian = Pembelian::where('invoice', $invoice)
            ->where('divisi_id', $divisiId)
            ->first();

        // Jika tidak ada pembelian dengan invoice & divisi yang sama, buat baru
        if (!$existingPembelian && $invoice) {
            Pembelian::create([
                'divisi_id' => $divisiId,
                'invoice' => $invoice,
                'status' => '1',
            ]);
        }

        return redirect()->route('admin.item.editItem')->withSuccess('Data berhasil disimpan');
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
