<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\User;
use App\Models\Sale;
use App\Models\ItemSale;
use App\Models\AccessoriesSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * DASHBOARD
     */
    public function index()
    {
        $userLogin = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | DATA DIVISI
        |--------------------------------------------------------------------------
        */

        $divisi = Divisi::all();

        $totaldivisiactive = Divisi::where('status', 'active')
            ->count();

        $totaldivisi = Divisi::count();


        /*
        |--------------------------------------------------------------------------
        | DATA ITEM
        |--------------------------------------------------------------------------
        */

        $itemsByCategory = Item::with('cat')
            ->select(
                'itemcategory_id',
                DB::raw('COUNT(*) as total'),
                DB::raw(
                    'SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as available'
                )
            )
            ->groupBy('itemcategory_id')
            ->get();

        $item = Item::count();


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin')
                ->where('name', '!=', 'manager');
        })->get();


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI MENDEKATI JATUH TEMPO
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $threeDaysLater = $today->copy()->addDays(3);

        $sales = Sale::where(
            'divisi_id',
            $userLogin->divisi_id
        )
            ->where('status_return', 0)

            ->whereHas('divisi', function ($query) {
                $query->where('status', 'active')
                    ->whereRaw('LOWER(name) != ?', ['rental']);
            })

            ->with([
                'customer',
                'user',
                'divisi',
                'itemSales.itemCategory',
                'accessoriesSales.accessories',
            ])

            ->where('deadlines', '<=', $threeDaysLater)

            ->orderBy('deadlines', 'asc')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | FORMAT NOMOR INVOICE
        |--------------------------------------------------------------------------
        */

        foreach ($sales as $data) {

            $transactionCount = Sale::where('id', '<=', $data->id)
                ->count();

            $nextNumber = str_pad(
                $transactionCount,
                4,
                '0',
                STR_PAD_LEFT
            );

            $currentYear = date('Y');

            $currentMonthNumber = date('n');

            $currentMonthRoman = $this->convertToRoman(
                $currentMonthNumber
            );

            $data->invoiceNumber =
                "INV/DND/{$nextNumber}/{$currentMonthRoman}/{$currentYear}";
        }


        /*
        |--------------------------------------------------------------------------
        | DEFAULT CHART
        |--------------------------------------------------------------------------
        */

        $chartData = $this->getChartData(
            'tahun',
            now()->year,
            now()->month
        );


        return view(
            'admin.index',
            compact(
                'sales',
                'user',
                'itemsByCategory',
                'item',
                'divisi',
                'totaldivisi',
                'totaldivisiactive',
                'chartData'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHART AJAX
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

        $tanggal = $request->input('tanggal');


        $chartData = $this->getChartData(
            $filter,
            $tahun,
            $bulan,
            $tanggal
        );


        return response()->json($chartData);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE CHART DATA
    |--------------------------------------------------------------------------
    |
    | SEMUA DATA CHART BERDASARKAN TABEL SALES.
    |
    | Syarat Sale:
    | - divisi_id = divisi user login
    | - status_return = 0
    | - divisi status = active
    | - divisi bukan Rental
    |
    |--------------------------------------------------------------------------
    */

    private function getChartData(
        $filter,
        $tahun,
        $bulan,
        $tanggal = null
    ) {

        $userLogin = Auth::user();

        $divisiId = $userLogin->divisi_id;


        /*
        |--------------------------------------------------------------------------
        | CEK DIVISI ADMIN
        |--------------------------------------------------------------------------
        */

        $divisiAktif = Divisi::where('id', $divisiId)
            ->where('status', 'active')
            ->whereRaw('LOWER(name) != ?', ['rental'])
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | JIKA DIVISI TIDAK AKTIF
        |--------------------------------------------------------------------------
        */

        if (!$divisiAktif) {

            return [
                'labels' => [],
                'transactions' => [],
                'items' => [],
                'accessories' => [],

                'total' => [
                    'transactions' => 0,
                    'items' => 0,
                    'accessories' => 0,
                ],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | BASE SALES
        |--------------------------------------------------------------------------
        |
        | INI ADALAH SUMBER UTAMA DATA CHART.
        |
        */

        $saleBase = Sale::query()
            ->where('divisi_id', $divisiId)

            // Hanya transaksi yang belum return
            ->where('status_return', 0)

            // Divisi harus aktif dan bukan Rental
            ->whereHas('divisi', function ($query) {

                $query->where('status', 'active')
                    ->whereRaw(
                        'LOWER(name) != ?',
                        ['rental']
                    );
            });


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
        | FILTER TAHUN
        |--------------------------------------------------------------------------
        |
        | Januari - Desember
        |
        */

        if ($filter === 'tahun') {

            $namaBulan = [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'Mei',
                'Jun',
                'Jul',
                'Agu',
                'Sep',
                'Okt',
                'Nov',
                'Des',
            ];


            for ($i = 1; $i <= 12; $i++) {

                $labels[] = $namaBulan[$i - 1];


                /*
                |--------------------------------------------------------------------------
                | SALES PADA BULAN TERSEBUT
                |--------------------------------------------------------------------------
                */

                $salesPeriode = (clone $saleBase)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $i)
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $transactions[] = $salesPeriode->count();


                /*
                |--------------------------------------------------------------------------
                | ITEM SALES
                |--------------------------------------------------------------------------
                |
                | HANYA item dari Sale yang status_return = 0
                |
                */

                $saleIds = $salesPeriode->pluck('id');


                $items[] = ItemSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | ACCESSORIES SALES
                |--------------------------------------------------------------------------
                |
                | HANYA accessories dari Sale yang status_return = 0
                |
                */

                $accessories[] = AccessoriesSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->sum('qty');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER BULAN
        |--------------------------------------------------------------------------
        |
        | 1 - 28/29/30/31
        |
        */

        elseif ($filter === 'bulan') {

            $startMonth = Carbon::create(
                $tahun,
                $bulan,
                1
            )->startOfMonth();


            $jumlahHari = $startMonth->daysInMonth;


            for ($i = 1; $i <= $jumlahHari; $i++) {

                $tanggalChart = $startMonth
                    ->copy()
                    ->day($i);


                $start = $tanggalChart
                    ->copy()
                    ->startOfDay();

                $end = $tanggalChart
                    ->copy()
                    ->endOfDay();


                /*
                |--------------------------------------------------------------------------
                | LABEL
                |--------------------------------------------------------------------------
                */

                $labels[] = $i;


                /*
                |--------------------------------------------------------------------------
                | SALES PADA TANGGAL TERSEBUT
                |--------------------------------------------------------------------------
                */

                $salesPeriode = (clone $saleBase)
                    ->whereBetween(
                        'created_at',
                        [$start, $end]
                    )
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $transactions[] = $salesPeriode->count();


                /*
                |--------------------------------------------------------------------------
                | SALE ID
                |--------------------------------------------------------------------------
                */

                $saleIds = $salesPeriode->pluck('id');


                /*
                |--------------------------------------------------------------------------
                | ITEM SALES
                |--------------------------------------------------------------------------
                */

                $items[] = ItemSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | ACCESSORIES SALES
                |--------------------------------------------------------------------------
                */

                $accessories[] = AccessoriesSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->sum('qty');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER MINGGU
        |--------------------------------------------------------------------------
        |
        | 1 minggu = Senin - Minggu
        |
        | Chart menampilkan 7 hari.
        |
        */

        elseif ($filter === 'minggu') {

            /*
            | Jika tanggal tidak diberikan,
            | gunakan hari ini.
            */

            if ($tanggal) {

                $tanggalAwal = Carbon::parse($tanggal);

            } else {

                $tanggalAwal = Carbon::today();
            }


            /*
            | Mulai dari hari Senin
            */

            $tanggalAwal = $tanggalAwal
                ->startOfWeek(Carbon::MONDAY);


            for ($i = 0; $i < 7; $i++) {

                $tanggalChart = $tanggalAwal
                    ->copy()
                    ->addDays($i);


                $start = $tanggalChart
                    ->copy()
                    ->startOfDay();

                $end = $tanggalChart
                    ->copy()
                    ->endOfDay();


                /*
                |--------------------------------------------------------------------------
                | LABEL
                |--------------------------------------------------------------------------
                */

                $labels[] = $tanggalChart
                    ->locale('id')
                    ->translatedFormat('D d');


                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                $salesPeriode = (clone $saleBase)
                    ->whereBetween(
                        'created_at',
                        [$start, $end]
                    )
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $transactions[] = $salesPeriode->count();


                /*
                |--------------------------------------------------------------------------
                | SALE ID
                |--------------------------------------------------------------------------
                */

                $saleIds = $salesPeriode->pluck('id');


                /*
                |--------------------------------------------------------------------------
                | ITEM SALES
                |--------------------------------------------------------------------------
                */

                $items[] = ItemSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | ACCESSORIES SALES
                |--------------------------------------------------------------------------
                */

                $accessories[] = AccessoriesSale::query()
                    ->where('status_return', 0)
                    ->whereIn('sale_id', $saleIds)
                    ->sum('qty');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        return [

            'labels' => $labels,

            'transactions' => $transactions,

            'items' => $items,

            'accessories' => $accessories,

            'total' => [

                'transactions' => array_sum(
                    $transactions
                ),

                'items' => array_sum(
                    $items
                ),

                'accessories' => array_sum(
                    $accessories
                ),

            ],

        ];
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


    /*
    |--------------------------------------------------------------------------
    | ROMAN MONTH
    |--------------------------------------------------------------------------
    */

    private function convertToRoman($monthNumber)
    {
        $months = [

            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',

        ];

        return $months[$monthNumber];
    }
}
