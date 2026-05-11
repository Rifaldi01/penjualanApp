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
        $divisi = $request->divisi_id;

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

        $divisis = Divisi::where('id', '!=', 6)->get();
        return view('gudang.index', compact(
            'itemsByCategory',
            'item',
            'sales',
            'divisis',
            'year',
            'divisi'
        ));
    }
    public function error(){
        return view('errors.500');
    }
}
