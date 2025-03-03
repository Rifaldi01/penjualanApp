<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

}
