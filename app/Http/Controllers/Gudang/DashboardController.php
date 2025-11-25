<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $itemsByCategory = Item::where('divisi_id', Auth::user()->divisi_id)->with('cat')
            ->select('itemcategory_id',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();
        $item = Item::where('divisi_id', Auth::user()->divisi_id)->count();
        $sales = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->with(['customer', 'user', 'itemSales.itemCategory', 'accessoriesSales.accessories'])
            ->get();
        return view('gudang.index', compact('itemsByCategory', 'item', 'sales'));
    }
    public function error(){
        return view('errors.500');
    }
}
