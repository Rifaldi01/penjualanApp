<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesIn;
use App\Models\Item;
use App\Models\ItemIn;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Hapus Data!';
        $text = "Yakin Ingin Menghapus Data Ini?";
        confirmDelete($title, $text);
        $pembelian = Pembelian::where('divisi_id', Auth::user()->divisi_id)->get();

        // Hitung total harga dari pembelian yang memiliki status 1
        $totalHarga = Pembelian::where('status', 1)->sum('total_harga');

        // Kembalikan data ke view
        return view('admin.pembelian.index', compact('pembelian', 'totalHarga'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('admin.pembelian.store'),
            'supplier' => Supplier::where('divisi_id', Auth::user()->divisi_id)->pluck('name', 'id')->toArray(),
        ];

        if ($id){
            $pembelian = Pembelian::whereId($id)->first();
            $inject = [
                'url' => route('admin.pembelian.update', $id),
                'supplier' => Supplier::where('divisi_id', Auth::user()->divisi_id)->pluck('name', 'id')->toArray(),
                'pembelian' => $pembelian
            ];
        }

        $item = ItemIn::all();
        $acces = AccessoriesIn::with('accessories')->get();

        // Gabungkan invoice dari accessories dan items, pastikan hanya satu kode invoice yang muncul
        $invoices = collect($item->pluck('kode_msk'))->merge($acces->pluck('kode_msk'))->unique();

        return view('admin.pembelian.create', $inject,  compact('item', 'acces', 'invoices'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->save($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->create($id);
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
        return $this->save($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->itemBelis()->delete();
        $pembelian->delete();

        return redirect()->route('admin.pembelian.index')->with('success', 'Pembelian berhasil dihapus!');
    }
    public function save(Request $request, $id = null)
    {
        $request->validate([
            'supplier_id' => 'required',
            'invoice' => 'required',
            'status' => 'required',
            'items' => 'nullable|array', // Membolehkan items kosong
            'items.*.no_seri' => 'required_with:items|string', // Validasi hanya jika items tidak kosong
            'items.*.capital_price' => 'nullable:items|numeric',
            'items.*.price' => 'required_with:items|numeric',
            'acces' => 'nullable|array',
            'acces.*.code_acces' => 'required|string',
            'acces.*.capital_price' => 'nullable|numeric',
            'acces.*.price' => 'required|numeric',
        ]);

        // Ambil data pembelian yang akan diupdate
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->update([
            'supplier_id' => $request->supplier_id,
            'divisi_id' => Auth::user()->divisi_id,
            'invoice' => $request->invoice,
            'status' => $request->status,
        ]);

        // Update data untuk item_ins jika items ada
        if ($request->has('items') && count($request->items) > 0) {
            foreach ($request->items as $item) {
                ItemIn::where('kode_msk', $pembelian->invoice)
                    ->where('no_seri', $item['no_seri'])
                    ->update([
                        'capital_price' => $item['capital_price'] ?? 0,
                        'price' => $item['price'],
                        'ppn' => $item['ppn'] ?? 0,
                    ]);

                // Update tabel items (hanya capital_price dan price)
                Item::where('no_seri', $item['no_seri'])->update([
                    'capital_price' => $item['capital_price'] ?? 0,
                    'price' => $item['price'] ?? 0,
                ]);
            }
        }

        // Pastikan request acces tidak kosong sebelum memproses
        if ($request->has('acces') && count($request->acces) > 0) {
            foreach ($request->acces as $accessory) {
                // Ambil id accessory berdasarkan code_acces
                $accessoryId = Accessories::where('code_acces', $accessory['code_acces'])->value('id');

                // Pastikan id accessories ditemukan
                if ($accessoryId) {
                    // Update tabel accessories_ins
                    AccessoriesIn::where('kode_msk', $pembelian->invoice)
                        ->where('accessories_id', $accessoryId)
                        ->update([
                            'capital_price' => $accessory['capital_price'] ?? 0,
                            'price' => $accessory['price'],
                            'ppn' => $accessory['ppn'] ?? 0,
                        ]);

                    // Update tabel accessories (hanya capital_price dan price)
                    Accessories::where('id', $accessoryId)->update([
                        'capital_price' => $accessory['capital_price'] ?? 0,
                        'price' => $accessory['price'],
                    ]);
                }
            }
        }

        // **Hitung total_item**
        $totalAccessories = AccessoriesIn::where('kode_msk', $pembelian->invoice)->sum('qty');
        $totalItems = ItemIn::where('kode_msk', $pembelian->invoice)->count();
        $totalItem = $totalAccessories + $totalItems;

        // **Hitung total_price**
        $totalCapitalAccessories = AccessoriesIn::where('kode_msk', $pembelian->invoice)->sum(DB::raw('qty * capital_price'));
        $totalCapitalItems = ItemIn::where('kode_msk', $pembelian->invoice)->sum('capital_price');
        $totalPrice = $totalCapitalAccessories + $totalCapitalItems;

        // **Update total_item dan total_price di tabel pembelian**
        $pembelian->update([
            'total_item' => $totalItem,
            'total_harga' => $totalPrice,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Data Pembelian Berhasil Diperbarui');
    }



}
