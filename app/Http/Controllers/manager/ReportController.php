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

        // Ambil semua divisi untuk dropdown
        $divisi = Divisi::whereNotIn('name', ['Rental', 'rental'])->get();

        $report = Sale::whereYear('created_at', $currentYear)
            ->with('customer', 'accessories', 'itemSales', 'debt.bank')
            ->orderBy('created_at', 'asc')
            ->get();

        $report->each(function ($sale) {
            $sale->accessories_list = $sale->accessories->pluck('name')->implode(', ');

            $sale->itemSales = $sale->itemSales->map(function ($itemSale) {
                return $itemSale->name;
            })->implode(', ');

            $sale->debt = $sale->debt ? $sale->debt->map(function ($debt) {
                $bankOrDescription = $debt->bank ? $debt->bank->name : ($debt->description ?: 'Tidak ada informasi');
                return $debt->date_pay . ' (' . $bankOrDescription . ')';
            })->implode(', ') : '-';
        });

        // Hitung total income dan lainnya
        $income = $report->sum('pay');
        $diskon = $report->sum('diskon');
        $ongkir = $report->sum('ongkir');
        $ppn = $report->sum('ppn');
        $pph = $report->sum('pph');
        $fee = $report->sum('fee');

        $totalCapitalPriceItem = ItemSale::whereYear('created_at', $currentYear)->sum('capital_price');
        $totalCapitalPriceAcces = Accessories::sum('capital_price');

        $profit = $income - $totalCapitalPriceItem - $totalCapitalPriceAcces;

        return view('manager.report.index', compact(
            'report',
            'income',
            'profit',
            'diskon',
            'ongkir',
            'ppn',
            'pph',
            'divisi',
            'fee',
        ));
    }

    public function filter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $divisiId = $request->input('divisi_id');

        $query = Sale::query();

        // Filter tahun berjalan jika tidak ada filter tanggal
        if (!$startDate && !$endDate) {
            $query->whereYear('created_at', now()->year);
        }

        // Filter berdasarkan tanggal jika disediakan
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Filter berdasarkan divisi jika disediakan
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }

        $report = $query->with('customer', 'accessories', 'itemSales', 'debt.bank')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIncome = 0;
        $totalCapital = 0;
        $totalDiskon = 0;
        $totalOngkir = 0;
        $totalppn = 0;
        $totalpph = 0;
        $totalfee = 0;
        $totalCapitalPerSale = [];

        $report->each(function ($sale) use (&$totalIncome, &$totalCapital, &$totalDiskon, &$totalOngkir, &$totalppn, &$totalpph, &$totalCapitalPerSale, &$totalfee, &$totalprice) {
            $totalIncome += $sale->pay;
            $totalDiskon += $sale->diskon;
            $totalOngkir += $sale->ongkir;
            $totalppn += $sale->ppn;
            $totalpph += $sale->pph;
            $totalfee += $sale->fee;
            $totalprice += $sale->total_price;

            $accessoryCapital = $sale->accessories->sum(function ($accessory) {
                return $accessory->pivot->qty * $accessory->capital_price;
            });

            $itemCapital = $sale->itemSales->sum('capital_price');

            $capitalPerSale = $accessoryCapital + $itemCapital;
            $totalCapital += $capitalPerSale;

            $totalCapitalPerSale[$sale->id] = $capitalPerSale;

            $sale->accessories_list = $sale->accessories->pluck('name')->implode(', ');
            $sale->itemSales = $sale->itemSales->map(function ($itemSale) {
                return $itemSale->name . ' - (' . $itemSale->no_seri . ')';
            });

            $sale->debt = $sale->debt ? $sale->debt->map(function ($debt) {
                $bankOrDescription = $debt->bank ? $debt->bank->name : ($debt->description ?: 'Tidak ada informasi');
                return $debt->date_pay . ' (' . $bankOrDescription . ')';
            })->implode(', ') : '-';
        });

        $profit = $totalIncome - $totalCapital;

        return response()->json([
            'totalCapital' => $totalCapitalPerSale,
            'report' => $report,
            'income' => $totalIncome,
            'profit' => $profit,
            'diskon' => $totalDiskon,
            'ongkir' => $totalOngkir,
            'ppn' => $totalppn,
            'pph' => $totalpph,
            'fee' => $totalfee,
            'totalprice' => $totalprice
        ]);
    }
}
