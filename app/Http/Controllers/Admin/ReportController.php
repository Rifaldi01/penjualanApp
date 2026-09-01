<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\ItemSale;
use App\Models\AccessoriesSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * ============================================================
     * LAPORAN TRANSAKSI
     * ============================================================
     */
    public function index()
    {
        return view('admin.report.index');
    }

    /**
     * ============================================================
     * FILTER / LOAD DATA
     * ============================================================
     */
    public function filter(Request $request)
    {
        try {
            // TIMEZONE
            $timezone = 'Asia/Jakarta';

            // USER LOGIN
            $user = Auth::user();

            if (!$user) return response()->json(['error' => 'User belum login.'], 401);

            // DIVISI ADMIN
            $divisiId = $user->divisi_id;

            // VALIDASI DIVISI
            if (!$divisiId) return response()->json(['error' => 'User belum memiliki divisi.'], 422);

            // DEFAULT FILTER TANGGAL
            if (!$request->filled('start_date') && !$request->filled('end_date')) {
                $startDate = Carbon::now($timezone)->startOfMonth()->startOfDay();
                $endDate = Carbon::now($timezone)->endOfMonth()->endOfDay();
            } else {
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date, $timezone)->startOfDay() : Carbon::now($timezone)->startOfMonth()->startOfDay();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date, $timezone)->endOfDay() : Carbon::now($timezone)->endOfDay();
            }

            // VALIDASI TANGGAL
            if ($startDate->gt($endDate)) {
                return response()->json(['error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal berakhir.'], 422);
            }

            // SALE BERDASARKAN INVOICE
            $invoiceSaleIds = Sale::query()
                ->where('divisi_id', $divisiId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function ($query) {
                    $query->whereNotNull('invoice')->orWhereNotNull('inv_manual');
                })
                ->pluck('id');

            // SALE DENGAN PEMBAYARAN PADA PERIODE
            $paymentSaleIds = Sale::query()
                ->where('divisi_id', $divisiId)
                ->whereHas('debt', function ($query) use ($startDate, $endDate) {
                    $query->whereNotNull('date_pay')->whereBetween('date_pay', [$startDate, $endDate]);
                })
                ->where(function ($query) {
                    $query->whereNotNull('invoice')->orWhereNotNull('inv_manual');
                })
                ->pluck('id');

            // GABUNGKAN SALE
            $saleIds = $invoiceSaleIds->merge($paymentSaleIds)->unique()->values();

            // JIKA TIDAK ADA DATA
            if ($saleIds->isEmpty()) {
                return response()->json([
                    'report' => [],
                    'totalCapital' => [],
                    'totalprice' => 0,
                    'income' => 0,
                    'ppn' => 0,
                    'pph' => 0,
                    'diskon' => 0,
                    'ongkir' => 0,
                    'admin' => 0,
                    'fee' => 0,
                    'profit' => 0,
                    'footer' => [
                        'total_invoice' => 0,
                        'ppn' => 0,
                        'pph' => 0,
                        'diskon' => 0,
                        'ongkir' => 0,
                        'admin' => 0,
                        'diterima' => 0,
                        'piutang' => 0,
                        'total_bayar' => 0,
                        'fee' => 0,
                        'modal' => 0,
                        'laba' => 0,
                    ],
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'divisi_id' => $divisiId,
                    'total_transaction' => 0,
                ]);
            }

            // AMBIL SALE
            $sales = Sale::with(['customer', 'debt.bank'])
                ->where('divisi_id', $divisiId)
                ->whereIn('id', $saleIds)
                ->orderBy('created_at', 'ASC')
                ->get();

            // TOTAL
            $totalInvoice = 0;
            $totalPPN = 0;
            $totalPPH = 0;
            $totalDiskon = 0;
            $totalOngkir = 0;
            $totalAdmin = 0;
            $totalDiterima = 0;
            $totalPiutang = 0;
            $totalBayar = 0;
            $totalFee = 0;
            $totalLaba = 0;
            $totalIncome = 0;
            $totalCapitalItem = 0;
            $totalCapitalAcc = 0;
            $totalCapital = [];
            $report = [];

            // LOOP SALE
            foreach ($sales as $sale) {
                // CEK INVOICE PERIODE
                $invoiceInPeriod = false;

                if ($sale->created_at) {
                    $invoiceDate = Carbon::parse($sale->created_at, $timezone);
                    $invoiceInPeriod = $invoiceDate->between($startDate, $endDate);
                }

                // ITEM SALES
                $itemSales = ItemSale::query()
                    ->where('sale_id', $sale->id)
                    ->where('status_return', 0)
                    ->whereNull('deleted_at')
                    ->get([
                        'id',
                        'sale_id',
                        'divisi_id',
                        'itemcategory_id',
                        'name',
                        'no_seri',
                        'price',
                        'capital_price',
                        'price_bottom',
                        'region',
                        'date_in',
                        'status_return',
                        'created_at',
                    ]);

                // ACCESSORIES SALES
                $accessoriesSales = AccessoriesSale::with(['accessories'])
                    ->where('sale_id', $sale->id)
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->whereNull('return_qty')->orWhereColumn('return_qty', '<', 'qty');
                    })
                    ->get();

                // TOTAL ITEM
                $totalItem = (int) ($sale->total_item ?? 0);

                // MODAL ITEM
                $capitalItem = $itemSales->sum(function ($item) {
                    return (float) ($item->capital_price ?? 0);
                });

                // MODAL ACCESSORIES
                $capitalAcc = 0;

                foreach ($accessoriesSales as $accessorySale) {
                    $qty = (float) ($accessorySale->qty ?? 0);
                    $returnQty = (float) ($accessorySale->return_qty ?? 0);
                    $qtyTersisa = max(0, $qty - $returnQty);
                    $capitalPrice = 0;

                    if ($accessorySale->accessories) {
                        $capitalPrice = (float) ($accessorySale->accessories->capital_price ?? 0);
                    }

                    $capitalAcc += $qtyTersisa * $capitalPrice;
                }

                // TOTAL MODAL
                $capitalPrice = $capitalItem + $capitalAcc;
                $totalCapital[$sale->id] = $capitalPrice;

                // NILAI SALE
                $totalPrice = (float) ($sale->total_price ?? 0);
                $ppn = (float) ($sale->ppn ?? 0);
                $pph = (float) ($sale->pph ?? 0);
                $diskon = (float) ($sale->diskon ?? 0);
                $ongkir = (float) ($sale->ongkir ?? 0);
                $adminFee = (float) ($sale->admin_fee ?? 0);
                $fee = (float) ($sale->fee ?? 0);

                // PEMBAYARAN PERIODE
                $periodPayments = $sale->debt->filter(function ($payment) use ($startDate, $endDate, $timezone) {
                    if (!$payment->date_pay) return false;

                    $datePay = Carbon::parse($payment->date_pay, $timezone);

                    return $datePay->between($startDate, $endDate);
                });

                // DITERIMA
                $diterima = $periodPayments->sum(function ($payment) {
                    return (float) ($payment->pay_debts ?? 0);
                });

                // TOTAL PEMBAYARAN SAMPAI END DATE
                $paidUntilEndDate = $sale->debt
                    ->filter(function ($payment) use ($endDate, $timezone) {
                        if (!$payment->date_pay) return false;

                        $datePay = Carbon::parse($payment->date_pay, $timezone);

                        return $datePay->lte($endDate);
                    })
                    ->sum(function ($payment) {
                        return (float) ($payment->pay_debts ?? 0);
                    });

                // TOTAL BAYAR
                $totalPay = $diterima;

                // PIUTANG
                $piutang = max(0, $totalPrice - $paidUntilEndDate);

                // INCOME
                if ($invoiceInPeriod) {
                    $income = $totalPrice;
                } else {
                    $income = 0;
                }

                // LABA RUGI
                if ($invoiceInPeriod) {
                    $feeForPeriod = $fee;
                    $profit = $totalPrice - $capitalItem - $capitalAcc - $feeForPeriod;
                } else {
                    $feeForPeriod = 0;
                    $profit = 0;
                }

                // PAYMENT DETAIL
                $paymentDetails = [];

                foreach ($periodPayments as $payment) {
                    $bankName = '';

                    if ($payment->bank) $bankName = $payment->bank->name;
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

                // ITEM DATA
                $itemSalesData = [];

                foreach ($itemSales as $itemSale) {
                    $itemSalesData[] = [
                        'id' => $itemSale->id,
                        'sale_id' => $itemSale->sale_id,
                        'name' => $itemSale->name ?? '',
                        'no_seri' => $itemSale->no_seri ?? '',
                        'price' => (float) ($itemSale->price ?? 0),
                        'capital_price' => (float) ($itemSale->capital_price ?? 0),
                        'price_bottom' => (float) ($itemSale->price_bottom ?? 0),
                        'region' => $itemSale->region ?? '',
                        'date_in' => $itemSale->date_in ?? null,
                        'status_return' => $itemSale->status_return,
                    ];
                }

                // ACCESSORIES DATA
                $accessoriesData = [];

                foreach ($accessoriesSales as $accessorySale) {
                    $qty = (float) ($accessorySale->qty ?? 0);
                    $returnQty = (float) ($accessorySale->return_qty ?? 0);
                    $qtyTersisa = max(0, $qty - $returnQty);
                    $accessoryName = '';
                    $accessoryCapitalPrice = 0;

                    if ($accessorySale->accessories) {
                        $accessoryName = $accessorySale->accessories->name ?? '';
                        $accessoryCapitalPrice = (float) ($accessorySale->accessories->capital_price ?? 0);
                    }

                    if (!$accessoryName) $accessoryName = $accessorySale->name ?? '';

                    $accessoriesData[] = [
                        'id' => $accessorySale->id,
                        'name' => $accessoryName,
                        'qty' => $qtyTersisa,
                        'price_sale' => (float) ($accessorySale->price_sale ?? 0),
                        'capital_price' => $accessoryCapitalPrice,
                        'return_qty' => $returnQty,
                    ];
                }

                // TOTAL FOOTER
                if ($invoiceInPeriod) {
                    $totalInvoice += $totalPrice;
                    $totalPPN += $ppn;
                    $totalPPH += $pph;
                    $totalDiskon += $diskon;
                    $totalOngkir += $ongkir;
                    $totalAdmin += $adminFee;
                    $totalIncome += $income;
                    $totalCapitalItem += $capitalItem;
                    $totalCapitalAcc += $capitalAcc;
                    $totalFee += $feeForPeriod;
                    $totalLaba += $profit;
                    $totalPiutang += $piutang;
                }

                // PEMBAYARAN
                $totalDiterima += $diterima;
                $totalBayar += $totalPay;

                // REPORT
                $report[] = [
                    'id' => $sale->id,
                    'divisi_id' => $sale->divisi_id,
                    'created_at' => $sale->created_at,
                    'invoice' => $sale->invoice,
                    'inv_manual' => $sale->inv_manual,
                    'customer' => $sale->customer,
                    'itemSales' => $itemSalesData,
                    'accessories' => $accessoriesData,
                    'total_item' => $totalItem,
                    'total_price' => $totalPrice,
                    'ppn' => $ppn,
                    'pph' => $pph,
                    'diskon' => $diskon,
                    'ongkir' => $ongkir,
                    'admin_fee' => $adminFee,
                    'nominal_in' => $diterima,
                    'pay' => $totalPay,
                    'piutang' => $piutang,
                    'fee' => $fee,
                    'profit' => $profit,
                    'debt' => $paymentDetails,
                    'capital_price' => $capitalPrice,
                    'paid_until_end_date' => $paidUntilEndDate,
                    'diterima_period' => $diterima,
                    'invoice_in_period' => $invoiceInPeriod,
                ];
            }

            // FOOTER
            $footer = [
                'total_invoice' => $totalInvoice,
                'ppn' => $totalPPN,
                'pph' => $totalPPH,
                'diskon' => $totalDiskon,
                'ongkir' => $totalOngkir,
                'admin' => $totalAdmin,
                'diterima' => $totalDiterima,
                'piutang' => $totalPiutang,
                'total_bayar' => $totalBayar,
                'fee' => $totalFee,
                'modal' => $totalCapitalItem + $totalCapitalAcc,
                'laba' => $totalLaba,
            ];

            // RESPONSE
            return response()->json([
                'report' => $report,
                'totalCapital' => $totalCapital,
                'totalprice' => $totalInvoice,
                'income' => $totalIncome,
                'ppn' => $totalPPN,
                'pph' => $totalPPH,
                'diskon' => $totalDiskon,
                'ongkir' => $totalOngkir,
                'admin' => $totalAdmin,
                'fee' => $totalFee,
                'profit' => $totalLaba,
                'footer' => $footer,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'divisi_id' => $divisiId,
                'total_transaction' => count($report),
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
