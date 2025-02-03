<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userDivisi = Divisi::where('name')->get();
        $divisi = Divisi::all();
        $title = 'Hapus Data!';
        $text = "Yakin Ingin Menghapus Data Ini?";
        confirmDelete($title, $text);
        $pembelian = Pembelian::all();

        $totalHarga = Pembelian::where('status', 1)->sum('total_harga');

        return view('manager.pembelian.index', compact('pembelian', 'totalHarga', 'userDivisi', 'divisi'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('manager.pembelian.store'),
            'supplier' => Supplier::pluck('name', 'id')->toArray(),
            'divisi' => Divisi::pluck('name', 'id')->toArray(),
        ];
        if ($id){
            $pembelian = Pembelian::whereId($id)->first();
            $inject = [
                'url' => route('manager.pembelian.update', $id),
                'supplier' => Supplier::pluck('name', 'id')->toArray(),
                'divisi' => Divisi::pluck('name', 'id')->toArray(),
                'pembelian' => $pembelian
            ];
        }
        return view('manager.pembelian.create', $inject);
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

        return redirect()->route('manager.pembelian.index')->with('success', 'Pembelian berhasil dihapus!');
    }
    private function save(Request $request, $id = null)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'divisi_id' => 'required',
            'invoice' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'status' => 'required|in:0,1',
            'items.*.name' => 'required|string|max:255',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.ppn' => 'nullable',
        ]);

        $totalItem = 0;
        $totalHarga = 0;
        $totalPpn = 0;

        foreach ($validated['items'] as $item) {
            $totalItem += $item['qty'];
            $totalPpn += $item['ppn'] * $item['qty'];
            $totalHarga += $item['harga'] * $item['qty'] + $totalPpn;
        }

        $pembelian = $id ? Pembelian::findOrFail($id) : new Pembelian;
        $pembelian->fill([
            'supplier_id' => $validated['supplier_id'],
            'divisi_id' => $validated['divisi_id'],
            'invoice' => $validated['invoice'],
            'status' => $validated['status'],
            'total_item' => $totalItem,
            'total_harga' => $totalHarga,
        ]);
        $pembelian->save();

        // Simpan item pembelian
        $pembelian->ItemBeli()->delete();
        foreach ($validated['items'] as $item) {
            $pembelian->ItemBeli()->create($item);
        }

        return redirect()->route('manager.pembelian.index')->with('success', 'Pembelian berhasil disimpan!');

    }
    public function filterByDivisi($divisiId = null)
    {
        // Jika tidak ada divisi dipilih, tampilkan data berdasarkan divisi user yang login
        $pembelian = Pembelian::with('supplier', 'ItemBeli') // Memastikan relasi dimuat dengan benar
        ->where('divisi_id', $divisiId)
            ->get();

        return response()->json($pembelian);
    }
}
