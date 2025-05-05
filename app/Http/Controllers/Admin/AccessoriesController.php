<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesIn;
use App\Models\AccessoriesSale;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AccessoriesController extends Controller
{
    public function index()
    {
        $userDivisi = auth()->user()->divisi->name; // Asumsikan user memiliki relasi ke divisi
        $acces = Accessories::with('divisi')->get();
        $divisi = Divisi::all();

        return view('admin.accessories.index', compact('acces', 'divisi', 'userDivisi'));
    }

    public function sale()
    {
        $sale = AccessoriesSale::whereHas('sale.divisi', function ($query){
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
        ->with('sale', 'accessories')->get();
        return view('admin.accessories.sale', compact('sale'));
    }
    public function filterByDivisi($divisiId = null)
    {
        // Jika tidak ada divisi dipilih, tampilkan data berdasarkan divisi user yang login
        if ($divisiId) {
            // Ambil aksesoris yang hanya terkait dengan divisi yang dipilih
            $acces = Accessories::where('divisi_id', $divisiId)->with('divisi')->get();
        } else {
            // Ambil semua aksesoris jika tidak ada filter divisi
            $acces = Accessories::with('divsis')->get();
        }

        return response()->json($acces);
    }

    public function editAcces()
    {
        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $acces = Accessories::where('divisi_id', Auth::user()->divisi_id)->get(); // Mengambil data accessories terbaru dengan pagination


        return view('admin.editAcces.index', compact('acces'));
    }

    public function edit($id){
        $acces = Accessories::whereId($id)->with('divisi')->first();
        return view('admin.editAcces.edit', compact('acces'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'price' => 'required'
        ], [
            'name.required' => 'Nama Accessories Wajib Diisi',
            'price.required' => 'Price Accessories Wajib Diisi',
        ]);


        $acces = Accessories::firstOrNew(['id' => $id]);
        $oldPrice = $acces->price; // Simpan harga lama
        $oldCapitalPrice = $acces->capital_price; // Simpan harga modal lama

        // Update data accessories
        $acces->name = $request->input('name');
        $acces->price = $request->input('price');
        $acces->capital_price = $request->input('capital_price');


        $acces->save();

        // Jika data accessories berhasil diupdate, update juga accessories_ins
        if ($id !== null) {
            AccessoriesIn::where('accessories_id', $id)
                ->update([
                    'price' => $request->input('price'),
                    'capital_price' => $request->input('capital_price')
                ]);
        }

        Alert::success('Success', 'Save Data Success');
        return redirect()->route('admin.acces.editAcces');
    }

}
