<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
    public function filterByDivisi(Request $request)
    {
        $query = Accessories::with('divisi');

        // Filter berdasarkan divisi jika ada
        if ($request->has('divisi_id') && !empty($request->divisi_id)) {
            $query->where('divisi_id', $request->divisi_id);
        }

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan nomor urut otomatis
            ->make(true);
    }

}
