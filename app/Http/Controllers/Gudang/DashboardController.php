<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;

        // Logika pembatasan divisi
        $user = auth()->user();

        if ($user->divisi_id != 1) {
            // Jika bukan divisi 1, kunci ke divisi milik user
            $divisi = $user->divisi_id;
        } else {
            // Jika divisi 1, ambil dari filter request (bisa null/semua)
            $divisi = $request->divisi_id;
        }

        // FILTER ITEM
        $itemQuery = Item::query();

        if ($divisi) {
            $itemQuery->where('divisi_id', $divisi);
        }

        $itemsByCategory = $itemQuery
            ->with('cat')
            ->select(
                'itemcategory_id',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();

        $item = (clone $itemQuery)->count();

        // FILTER SALES
        $salesQuery = Sale::query();

        if ($divisi) {
            $salesQuery->where('divisi_id', $divisi);
        }

        $sales = $salesQuery
            ->whereYear('created_at', $year)
            ->with([
                'customer',
                'user',
                'itemSales.itemCategory',
                'accessoriesSales.accessories'
            ])
            ->latest()
            ->get();

        // Opsional: Jika user bukan divisi 1,
        // daftar divisi di dropdown mungkin hanya perlu menampilkan divisinya saja
        $divisis = Divisi::where('id', '!=', 6);
        if ($user->divisi_id != 1) {
            $divisis->where('id', $user->divisi_id);
        }
        $divisis = $divisis->get();

        return view('gudang.index', compact(
            'itemsByCategory',
            'item',
            'sales',
            'divisis',
            'year',
            'divisi'
        ));
    }
}
