<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\ItemSale;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;

        // Filter laporan berdasarkan divisi pengguna dan tahun berjalan
        $report = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->whereYear('created_at', $currentYear)
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

        // Hitung total income, diskon, ongkir, dan profit untuk tahun berjalan
        $income = $report->sum('pay');
        $diskon = $report->sum('diskon');
        $ongkir = $report->sum('ongkir');
        $ppn = $report->sum('ppn');
        $pph = $report->sum('pph');

        $totalCapitalPriceItem = ItemSale::whereHas('sale', function ($query) use ($currentYear) {
            $query->where('divisi_id', Auth::user()->divisi_id)
                ->whereYear('created_at', $currentYear);
        })->sum('capital_price');

        $totalCapitalPriceAcces = Accessories::whereHas('divisi', function ($query) {
            $query->where('divisi_id', Auth::user()->divisi_id);
        })->sum('capital_price');

        $profit = $income - $totalCapitalPriceItem - $totalCapitalPriceAcces;

        return view('admin.report.index', compact('report', 'income', 'profit', 'diskon', 'ongkir', 'ppn', 'pph'));
    }

    public function filter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::query();

        // Filter divisi
        $query->where('divisi_id', Auth::user()->divisi_id);

        // Filter tahun berjalan jika tidak ada filter tanggal
        if (!$startDate && !$endDate) {
            $query->whereYear('created_at', now()->year);
        }

        // Filter tanggal jika disediakan
        if ($startDate && $endDate) {
            // Tambahkan waktu 00:00:00 ke start dan 23:59:59 ke end
            $start = Carbon::parse($startDate)->startOfDay(); // 00:00:00
            $end = Carbon::parse($endDate)->endOfDay();       // 23:59:59

            $query->whereBetween('created_at', [$start, $end]);
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

        $report->each(function ($sale) use (&$totalIncome, &$totalCapital, &$totalDiskon, &$totalOngkir, &$totalppn, &$totalpph) {
            $totalIncome += $sale->pay;
            $totalDiskon += $sale->diskon;
            $totalOngkir += $sale->ongkir;
            $totalppn += $sale->ppn;
            $totalpph += $sale->pph;

            $accessoryCapital = $sale->accessories->sum(function ($accessory) {
                return $accessory->pivot->qty * $accessory->capital_price;
            });

            $itemCapital = $sale->itemSales->sum(function ($itemSale) {
                return $itemSale->capital_price;
            });

            $totalCapital += $accessoryCapital + $itemCapital;

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
            'report' => $report,
            'income' => $totalIncome,
            'profit' => $profit,
            'diskon' => $totalDiskon,
            'ongkir' => $totalOngkir,
            'ppn' => $totalppn,
            'pph' => $totalpph,
        ]);
    }
}
