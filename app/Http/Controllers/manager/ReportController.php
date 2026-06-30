<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\Divisi;
use App\Models\ItemSale;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $divisi = Divisi::whereNotIn('name', ['Rental', 'rental'])->get();

        $report = Sale::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->with([
                'customer',
                'debt.bank',
                'itemSales' => function ($q) {
                    $q->where('status_return', 0);
                },
                'accessoriesSales' => function ($q) {
                    $q->whereRaw('COALESCE(return_qty,0) < qty');
                },
                'accessoriesSales.accessories'
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $report->each(function ($sale) {

            $sale->accessories_list = $sale->accessoriesSales
                ->pluck('accessories.name')
                ->filter()
                ->implode(', ');

            $sale->itemSales = $sale->itemSales
                ->map(function ($itemSale) {
                    return $itemSale->name;
                })
                ->implode(', ');

            $sale->debt = $sale->debt
                ? $sale->debt->map(function ($debt) {
                    $bankOrDescription = $debt->bank
                        ? $debt->bank->name
                        : ($debt->description ?: 'Tidak ada informasi');

                    return $debt->date_pay . ' (' . $bankOrDescription . ')';
                })->implode(', ')
                : '-';
        });

        $income     = $report->sum('pay');
        $diskon     = $report->sum('diskon');
        $ongkir     = $report->sum('ongkir');
        $ppn        = $report->sum('ppn');
        $pph        = $report->sum('pph');
        $admin_fee  = $report->sum('admin_fee');
        $fee        = $report->sum('fee');
        $diterima   = $report->sum('nominal_in');

        $totalCapitalPriceItem = ItemSale::where('status_return',0)
            ->whereYear('created_at',$currentYear)
            ->whereMonth('created_at',$currentMonth)
            ->sum('capital_price');

        $totalCapitalPriceAcces = 0;

        foreach ($report as $sale) {

            foreach ($sale->accessoriesSales as $detail) {

                if (!$detail->accessories) {
                    continue;
                }

                $qtyTersisa = $detail->qty - ($detail->return_qty ?? 0);

                $totalCapitalPriceAcces +=
                    $qtyTersisa * $detail->accessories->capital_price;
            }
        }

        $profit = $income - $totalCapitalPriceItem - $totalCapitalPriceAcces;

        return view('manager.report.index', compact(
            'report',
            'income',
            'admin_fee',
            'profit',
            'diskon',
            'ongkir',
            'ppn',
            'pph',
            'divisi',
            'fee',
            'diterima'
        ));
    }

    public function filter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $divisiId  = $request->input('divisi_id');

        $query = Sale::query();

        if (!$startDate && !$endDate) {

            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);

        }

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }

        $report = $query->with([
            'customer',
            'debt.bank',
            'itemSales' => function ($q) {
                $q->where('status_return', 0);
            },
            'accessoriesSales' => function ($q) {
                $q->whereRaw('COALESCE(return_qty,0) < qty');
            },
            'accessoriesSales.accessories'
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIncome = 0;
        $totalCapital = 0;
        $totalDiskon = 0;
        $totalOngkir = 0;
        $totalppn = 0;
        $totalpph = 0;
        $totalfee = 0;
        $totalprice = 0;
        $admin_fee = 0;
        $diterima = 0;
        $totalPiutang = 0;

        $totalCapitalPerSale = [];

        $report->each(function ($sale) use (
            &$totalIncome,
            &$totalCapital,
            &$totalDiskon,
            &$totalOngkir,
            &$admin_fee,
            &$totalppn,
            &$totalpph,
            &$totalCapitalPerSale,
            &$totalfee,
            &$totalprice,
            &$diterima,
            &$totalPiutang,
        ) {

            $totalIncome += $sale->pay;
            $totalDiskon += $sale->diskon;
            $totalOngkir += $sale->ongkir;
            $totalppn += $sale->ppn;
            $totalpph += $sale->pph;
            $admin_fee += $sale->admin_fee;
            $totalfee += $sale->fee;
            $totalprice += $sale->total_price;
            $diterima += $sale->nominal_in;
            $totalPiutang += max(($sale->pay ?? 0) - ($sale->nominal_in ?? 0), 0);

            $accessoryCapital = 0;

            foreach ($sale->accessoriesSales as $detail) {

                if (!$detail->accessories) {
                    continue;
                }

                $qtyTersisa = $detail->qty - ($detail->return_qty ?? 0);

                $accessoryCapital +=
                    $qtyTersisa * $detail->accessories->capital_price;
            }

            $itemCapital = $sale->itemSales
                ->where('status_return', 0)
                ->sum('capital_price');

            $capitalPerSale = $accessoryCapital + $itemCapital;

            $totalCapital += $capitalPerSale;

            $totalCapitalPerSale[$sale->id] = $capitalPerSale;

            $sale->accessories_list = $sale->accessoriesSales
                ->pluck('accessories.name')
                ->filter()
                ->implode(', ');

            $sale->itemSales = $sale->itemSales->map(function ($itemSale) {
                return $itemSale->name . ' - (' . $itemSale->no_seri . ')';
            });

            $sale->debt = $sale->debt
                ? $sale->debt->map(function ($debt) {
                    $bankOrDescription = $debt->bank
                        ? $debt->bank->name
                        : ($debt->description ?: 'Tidak ada informasi');

                    return $debt->date_pay . ' (' . $bankOrDescription . ')';
                })->implode(', ')
                : '-';
        });

        $profit = $totalIncome - $totalCapital;
        return response()->json([
            'totalCapital' => $totalCapitalPerSale,
            'report'       => $report,
            'admin_fee'    => $admin_fee,
            'income'       => $totalIncome,
            'profit'       => $profit,
            'diskon'       => $totalDiskon,
            'ongkir'       => $totalOngkir,
            'ppn'          => $totalppn,
            'pph'          => $totalpph,
            'fee'          => $totalfee,
            'totalprice'   => $totalprice,
            'diterima'     => $diterima,

            'footer' => [
                'total_invoice' => $totalprice,
                'ppn'           => $totalppn,
                'pph'           => $totalpph,
                'diskon'        => $totalDiskon,
                'ongkir'        => $totalOngkir,
                'admin'         => $admin_fee,
                'diterima'      => $diterima,
                'piutang'       => $totalPiutang,
                'total_bayar'   => $totalIncome,
                'fee'           => $totalfee,
                'modal'         => $totalCapital,
                'laba'          => $profit,
            ]
        ]);
    }
}
