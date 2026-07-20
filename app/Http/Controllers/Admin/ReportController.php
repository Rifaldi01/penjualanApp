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
        $currentMonth = now()->month;

        // Filter laporan berdasarkan divisi pengguna dan tahun berjalan
        $report = Sale::where('divisi_id', Auth::user()->divisi_id)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
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
        $income    = $report->sum('pay');
        $diskon    = $report->sum('diskon');
        $ongkir    = $report->sum('ongkir');
        $ppn       = $report->sum('ppn');
        $pph       = $report->sum('pph');
        $admin = $report->sum('admin_fee');
        $fee       = $report->sum('fee');

        $totalCapitalPriceItem = ItemSale::whereYear('created_at', $currentYear)->whereMonth('created_at', $currentMonth)
            ->sum('capital_price');
        $totalCapitalPriceAcces = Accessories::sum('capital_price');

        $profit = $income - $totalCapitalPriceItem - $totalCapitalPriceAcces;

        return view('admin.report.index', compact(
            'report',
            'income',
            'profit',
            'diskon',
            'ongkir',
            'ppn',
            'pph',
            'admin',
            'fee',
        ));
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

            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);

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
        $totalfee = 0;
        $totalPiutang = 0;
        $diterima = 0;
        $admin = 0;
        $totalCapitalPerSale = [];

        $report->each(function ($sale) use (
            &$totalIncome,
            &$totalCapital,
            &$totalDiskon,
            &$totalOngkir,
            &$totalppn,
            &$totalpph,
            &$totalCapitalPerSale,
            &$totalfee,
            &$totalprice,
            &$totalPiutang,
            &$diterima,
            &$admin,
        ) {
            $totalIncome += $sale->pay;
            $totalDiskon += $sale->diskon;
            $totalOngkir += $sale->ongkir;
            $totalppn += $sale->ppn;
            $totalpph += $sale->pph;
            $totalfee += $sale->fee;
            $totalprice += $sale->total_price;
            $diterima += $sale->nominal_in;
            $admin += $sale->admin_fee;
            $totalPiutang += max(($sale->pay ?? 0) - ($sale->nominal_in ?? 0), 0);

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
            'report'       => $report,
            'admin'        => $admin,
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
                'admin'         => $admin,
                'diterima'      => $diterima,
                'piutang'       => $totalPiutang,
                'total_bayar'   => $totalIncome,
                'fee'           => $totalfee,
                'laba'          => $profit,
            ]
        ]);
    }
}
