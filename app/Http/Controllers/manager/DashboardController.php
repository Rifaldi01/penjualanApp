<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\User;
use App\Models\Sale;
use App\Models\ItemSale;
use App\Models\AccessoriesSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $itemsByCategory = Item::with('cat')
            ->select(
                'itemcategory_id',
                DB::raw('count(*) as total'),
                DB::raw('SUM(case when status = 0 then 1 else 0 end) as available')
            )
            ->groupBy('itemcategory_id')
            ->get();

        $item = Item::count();

        $divisi = Divisi::all();

        $totaldivisiactive = Divisi::where('status', 'active')->count();

        $totaldivisi = Divisi::count();

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin')
                ->where('name', '!=', 'manager');
        })->get();

        return view('manager.index', compact(
            'user',
            'itemsByCategory',
            'item',
            'divisi',
            'totaldivisi',
            'totaldivisiactive'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | DATA CHART DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function chart(Request $request)
    {
        $filter = $request->input('filter', 'tahun');

        $tahun = (int) $request->input(
            'tahun',
            now()->year
        );

        $bulan = (int) $request->input(
            'bulan',
            now()->month
        );


        /*
        |--------------------------------------------------------------------------
        | BASE QUERY SALE
        |--------------------------------------------------------------------------
        */

        $saleQuery = Sale::query()
            ->where('status_return', 0);


        /*
        |--------------------------------------------------------------------------
        | FILTER PERIODE
        |--------------------------------------------------------------------------
        */

        if ($filter === 'tahun') {

            $startDate = Carbon::create(
                $tahun,
                1,
                1
            )->startOfDay();

            $endDate = Carbon::create(
                $tahun,
                12,
                31
            )->endOfDay();

        } elseif ($filter === 'bulan') {

            $startDate = Carbon::create(
                $tahun,
                $bulan,
                1
            )->startOfMonth();

            $endDate = Carbon::create(
                $tahun,
                $bulan,
                1
            )->endOfMonth();

        }


        /*
        |--------------------------------------------------------------------------
        | LABEL
        |--------------------------------------------------------------------------
        */

        $labels = [];

        $transactions = [];

        $items = [];

        $accessories = [];


        /*
        |--------------------------------------------------------------------------
        | PER TAHUN
        |--------------------------------------------------------------------------
        */

        if ($filter === 'tahun') {

            for ($i = 1; $i <= 12; $i++) {

                $labels[] = Carbon::create()
                    ->month($i)
                    ->locale('id')
                    ->translatedFormat('M');

                $transactions[] = (clone $saleQuery)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $i)
                    ->count();

                $items[] = ItemSale::where(
                    'status_return',
                    0
                )
                    ->whereHas('sale', function ($query) use (
                        $tahun,
                        $i
                    ) {

                        $query
                            ->where('status_return', 0)
                            ->whereYear(
                                'created_at',
                                $tahun
                            )
                            ->whereMonth(
                                'created_at',
                                $i
                            );

                    })
                    ->count();

                $accessories[] = AccessoriesSale::where(
                    'status_return',
                    0
                )
                    ->whereHas('sale', function ($query) use (
                        $tahun,
                        $i
                    ) {

                        $query
                            ->where('status_return', 0)
                            ->whereYear(
                                'created_at',
                                $tahun
                            )
                            ->whereMonth(
                                'created_at',
                                $i
                            );

                    })
                    ->sum('qty');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PER BULAN
        |--------------------------------------------------------------------------
        */

        elseif ($filter === 'bulan') {

            $jumlahHari = $startDate->daysInMonth;

            for ($i = 1; $i <= $jumlahHari; $i++) {

                $labels[] = $i;

                $transactions[] = (clone $saleQuery)
                    ->whereDate(
                        'created_at',
                        $startDate
                            ->copy()
                            ->day($i)
                            ->format('Y-m-d')
                    )
                    ->count();


                $tanggal = $startDate
                    ->copy()
                    ->day($i)
                    ->format('Y-m-d');


                $items[] = ItemSale::where(
                    'status_return',
                    0
                )
                    ->whereHas('sale', function ($query) use (
                        $tanggal
                    ) {

                        $query
                            ->where(
                                'status_return',
                                0
                            )
                            ->whereDate(
                                'created_at',
                                $tanggal
                            );

                    })
                    ->count();


                $accessories[] = AccessoriesSale::where(
                    'status_return',
                    0
                )
                    ->whereHas('sale', function ($query) use (
                        $tanggal
                    ) {

                        $query
                            ->where(
                                'status_return',
                                0
                            )
                            ->whereDate(
                                'created_at',
                                $tanggal
                            );

                    })
                    ->sum('qty');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $totalTransactions = array_sum(
            $transactions
        );

        $totalItems = array_sum(
            $items
        );

        $totalAccessories = array_sum(
            $accessories
        );


        return response()->json([

            'labels' => $labels,

            'transactions' => $transactions,

            'items' => $items,

            'accessories' => $accessories,

            'total' => [

                'transactions' => $totalTransactions,

                'items' => $totalItems,

                'accessories' => $totalAccessories,

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | USER STATUS
    |--------------------------------------------------------------------------
    */

    public function userStatus()
    {
        $users = User::whereHas('roles', function ($query) {

            $query->where(
                'name',
                '!=',
                'superadmin'
            );

        })->get();


        $data = $users->map(function ($user) {

            return [

                'id' => $user->id,

                'online' => $user->isOnline(),

            ];

        });


        return response()->json($data);
    }
}
