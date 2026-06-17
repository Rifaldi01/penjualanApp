<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function salesReturn(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $user = auth()->user();

        $query = SalesReturn::with([
            'sale.customer',
            'sale.divisi',
            'user'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER DIVISI USER LOGIN
        |--------------------------------------------------------------------------
        */

        $query->whereHas('sale', function ($q) use ($user) {

            $q->where('divisi_id', $user->divisi_id);

        });

        /*
        |--------------------------------------------------------------------------
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        */

        if ($year != 'all') {

            $query->whereYear('created_at', $year);

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        */

        if ($month != 'all') {

            $query->whereMonth('created_at', $month);

        }

        $salesReturns = $query
            ->with([
                'returnItems.itemSale',
                'returnAccessories.accessories',
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA FILTER
        |--------------------------------------------------------------------------
        */

        $years = SalesReturn::whereHas('sale', function ($q) use ($user) {

            $q->where('divisi_id', $user->divisi_id);

        })
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('gudang.return.index', compact(
            'salesReturns',
            'years'
        ));
    }
}
