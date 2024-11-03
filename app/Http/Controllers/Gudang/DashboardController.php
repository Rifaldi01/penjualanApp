<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $itemsByCategory = Item::with('cat')
            ->select('itemcategory_id',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();
        $item = Item::all()->count();
        $sales = Sale::with(['customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])
            ->whereDate('created_at', Carbon::today())
            ->get();
        return view('gudang.index', compact('itemsByCategory', 'item', 'sales'));
    }
}
