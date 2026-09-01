<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Debt;
use App\Models\Divisi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * ============================================================
     * HALAMAN LAPORAN
     * ============================================================
     */
    public function index()
    {
        $divisis = Divisi::where('status', 'active')->where('name', '!=', 'Rental')->orderBy('name')->get();

        return view('manager.report.index', compact('divisis'));
    }

    /**
     * ============================================================
     * FILTER LAPORAN
     * ============================================================
     */
    public function filter(Request $request)
    {
        try {
            // TIMEZONE
            $timezone = 'Asia/Jakarta';

            // FILTER TANGGAL
            $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date, $timezone)->startOfDay() : Carbon::now($timezone)->startOfMonth();
            $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date, $timezone)->endOfDay() : Carbon::now($timezone)->endOfMonth();

            // VALIDASI
            if ($startDate->gt($endDate)) return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal berakhir.'], 422);

            // DIVISI
            $divisiId = $request->input('divisi_id');

            // AMBIL PEMBAYARAN PADA PERIODE
            $paymentsQuery = Debt::with('bank')->whereNotNull('date_pay')->whereBetween('date_pay', [$startDate, $endDate])->orderBy('date_pay');

            // FILTER DIVISI PEMBAYARAN
            if ($divisiId && $divisiId !== 'all') {
                $paymentsQuery->whereHas('sale', function ($query) use ($divisiId) {
                    $query->where('divisi_id', $divisiId);
                });
            }

            // GET PEMBAYARAN PERIODE
            $paymentsInPeriod = $paymentsQuery->get();

            // GROUP PEMBAYARAN BERDASARKAN SALE
            $groupedPayments = $paymentsInPeriod->groupBy('sale_id');

            // SALE ID
            $saleIds = $groupedPayments->keys()->filter(fn ($id) => !is_null($id))->values();

            // QUERY SALES
            $salesQuery = Sale::with([
                'customer',
                'divisi',
                'itemSales' => function ($query) {
                    $query->where('status_return', 0);
                },
                'accessories' => function ($query) {
                    $query->withPivot('qty');
                },
                'debt' => function ($query) use ($endDate) {
                    $query->with('bank')->whereNotNull('date_pay')->whereDate('date_pay', '<=', $endDate->toDateString())->orderBy('date_pay');
                }
            ])->whereIn('id', $saleIds);

            // FILTER DIVISI SALE
            if ($divisiId && $divisiId !== 'all') $salesQuery->where('divisi_id', $divisiId);

            // GET SALES
            $sales = $salesQuery->orderBy('created_at', 'asc')->get()->keyBy('id');
            // TOTAL FOOTER
            $totalInvoice = 0;
            $totalPpn = 0;
            $totalPph = 0;
            $totalDiskon = 0;
            $totalOngkir = 0;
            $totalAdmin = 0;
            $totalDiterima = 0;
            $totalPiutang = 0;
            $totalBayar = 0;
            $totalFee = 0;
            $totalLaba = 0;

            // TOTAL MODAL
            $totalCapital = [];

            // REPORT
            $report = [];

            // LOOP PEMBAYARAN
            foreach ($sales as $saleId => $sale) {
                $periodPayments = $groupedPayments->get($saleId, collect());

                if ($periodPayments->isEmpty()) continue;

                // TANGGAL INVOICE
                $saleCreatedAt = $sale->created_at ? Carbon::parse($sale->created_at, $timezone) : null;

                // CEK APAKAH INVOICE MASUK PERIODE
                $invoiceInPeriod = $saleCreatedAt ? $saleCreatedAt->between($startDate, $endDate) : false;

                // DATA INVOICE
                $invoice = (float) ($sale->total_price ?? 0);
                $ppn = (float) ($sale->ppn ?? 0);
                $pph = (float) ($sale->pph ?? 0);
                $diskon = (float) ($sale->diskon ?? 0);
                $ongkir = (float) ($sale->ongkir ?? 0);
                $admin = (float) ($sale->admin_fee ?? 0);
                $fee = (float) ($sale->fee ?? 0);

                // DITERIMA PADA PERIODE
                $diterima = $periodPayments->sum(fn ($payment) => (float) ($payment->pay_debts ?? 0));

                // TOTAL BAYAR SAMPAI END DATE
                $paidUntilEndDate = $sale->debt->sum(fn ($payment) => (float) ($payment->pay_debts ?? 0));

                // PIUTANG
                $piutang = max($invoice - $paidUntilEndDate - $diskon - $fee, 0);

                // TOTAL BAYAR
                $totalBayarSale = $paidUntilEndDate;

                // MODAL
                $capitalPrice = 0;

                // MODAL ITEM
                foreach ($sale->itemSales as $itemSale) {
                    $capitalPrice += (float) ($itemSale->capital_price ?? 0);
                }

                // MODAL ACCESSORIES
                foreach ($sale->accessories as $accessory) {
                    $qty = (float) ($accessory->pivot->qty ?? 0);
                    $capital = (float) ($accessory->capital_price ?? 0);
                    $capitalPrice += $qty * $capital;
                }

                // SIMPAN MODAL
                $totalCapital[$sale->id] = $capitalPrice;

                // TOTAL ITEM
                $totalItem = (int) ($sale->total_item ?? 0);

                // PAYMENT RATIO
                $paymentRatio = $invoice > 0 ? min($diterima / $invoice, 1) : 0;

                // MODAL PERIODE
                $capitalForPeriod = $capitalPrice * $paymentRatio;

                // FEE PERIODE
                $feeForPeriod = $fee * $paymentRatio;

                // LABA RUGI
                $cutoffFee = Carbon::create(2026, 7, 31, 23, 59, 0, $timezone);

                if ($endDate->lt($cutoffFee)) {
                    $laba = $sale->pay - $capitalForPeriod;
                } else {
                    $laba = $diterima - $capitalForPeriod - $feeForPeriod;
                }

                // DETAIL PEMBAYARAN
                $paymentDetails = [];

                foreach ($periodPayments as $payment) {
                    $bankName = $payment->bank ? $payment->bank->name : '';
                    if (!$bankName) $bankName = $payment->bank_name ?? '';

                    $description = $payment->description ?? '';

                    if ($bankName) {
                        $paymentMethod = $bankName;
                    } elseif ($description) {
                        $paymentMethod = $description;
                    } else {
                        $paymentMethod = 'Cash';
                    }

                    $paymentDetails[] = [
                        'id' => $payment->id,
                        'date_pay' => $payment->date_pay,
                        'pay_debts' => (float) ($payment->pay_debts ?? 0),
                        'bank_name' => $paymentMethod,
                        'description' => $description,
                        'penerima' => $payment->penerima ?? null,
                    ];
                }

                // ITEM SALES
                $itemSales = [];

                foreach ($sale->itemSales as $itemSale) {
                    $name = $itemSale->name ?? '';

                    $itemSales[] = [
                        'name' => $name,
                        'no_seri' => $itemSale->no_seri ?? '',
                        'price' => (float) ($itemSale->price ?? 0),
                        'capital_price' => (float) ($itemSale->capital_price ?? 0),
                        'status_return' => $itemSale->status_return ?? 0,
                    ];
                }

                // ACCESSORIES
                $accessories = [];

                foreach ($sale->accessories as $accessory) {
                    $accessories[] = [
                        'name' => $accessory->name ?? '',
                        'qty' => (float) ($accessory->pivot->qty ?? 0),
                        'price_sale' => (float) ($accessory->pivot->price_sale ?? $accessory->price ?? 0),
                    ];
                }

                // REPORT
                $report[] = [
                    'id' => $sale->id,
                    'created_at' => $sale->created_at,
                    'divisi' => $sale->divisi ? $sale->divisi->name : 'N/A',
                    'divisi_id' => $sale->divisi_id,
                    'invoice' => $sale->invoice,
                    'inv_manual' => $sale->inv_manual,
                    'customer' => $sale->customer,
                    'total_item' => $totalItem,
                    'total_price' => $invoice,
                    'ppn' => $ppn,
                    'pph' => $pph,
                    'diskon' => $diskon,
                    'ongkir' => $ongkir,
                    'admin_fee' => $admin,
                    'nominal_in' => $diterima,
                    'pay' => $totalBayarSale,
                    'piutang' => $piutang,
                    'fee' => $fee,
                    'profit' => $laba,
                    'itemSales' => $itemSales,
                    'accessories' => $accessories,
                    'debt' => $paymentDetails,
                    'invoice_in_period' => $invoiceInPeriod,
                    'capital_price' => $capitalPrice,
                    'paid_until_end_date' => $paidUntilEndDate,
                    'diterima_period' => $diterima,
                ];

                // FOOTER
                if ($invoiceInPeriod) {
                    $totalInvoice += $invoice;
                    $totalPpn += $ppn;
                    $totalPph += $pph;
                    $totalDiskon += $diskon;
                    $totalOngkir += $ongkir;
                    $totalAdmin += $admin;
                }

                // DITERIMA
                $totalDiterima += $diterima;

                // PIUTANG
                $totalPiutang += $piutang;

                // TOTAL BAYAR
                $totalBayar += $totalBayarSale;

                // FEE
                $totalFee += $feeForPeriod;

                // LABA
                $totalLaba += $laba;
            }

            // SUMMARY
            $income = $totalDiterima;

            // TOTAL PRICE / TOTAL INVOICE
            $totalprice = $totalInvoice;

            // RESPONSE
            return response()->json([
                'report' => $report,
                'totalCapital' => $totalCapital,
                'totalprice' => $totalprice,
                'income' => $income,
                'ppn' => $totalPpn,
                'pph' => $totalPph,
                'diskon' => $totalDiskon,
                'ongkir' => $totalOngkir,
                'admin' => $totalAdmin,
                'fee' => $totalFee,
                'profit' => $totalLaba,
                'footer' => [
                    'total_invoice' => $totalInvoice,
                    'ppn' => $totalPpn,
                    'pph' => $totalPph,
                    'diskon' => $totalDiskon,
                    'ongkir' => $totalOngkir,
                    'admin' => $totalAdmin,
                    'diterima' => $totalDiterima,
                    'piutang' => $totalPiutang,
                    'total_bayar' => $totalBayar,
                    'fee' => $totalFee,
                    'modal' => array_sum($totalCapital),
                    'laba' => $totalLaba,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
