<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\ItemSale;
use App\Models\AccessoriesSale;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            /*
            |--------------------------------------------------------------------------
            | TIMEZONE
            |--------------------------------------------------------------------------
            */

            $timezone = 'Asia/Jakarta';

            /*
            |--------------------------------------------------------------------------
            | DEFAULT FILTER
            |--------------------------------------------------------------------------
            |
            | Jika start_date dan end_date kosong:
            | otomatis menggunakan bulan berjalan.
            |
            */

            if (
                !$request->filled('start_date') &&
                !$request->filled('end_date')
            ) {

                $startDate = Carbon::now($timezone)
                    ->startOfMonth()
                    ->startOfDay();

                $endDate = Carbon::now($timezone)
                    ->endOfMonth()
                    ->endOfDay();

            } else {

                $startDate = $request->filled('start_date')
                    ? Carbon::parse(
                        $request->start_date,
                        $timezone
                    )->startOfDay()
                    : Carbon::now($timezone)
                        ->startOfMonth()
                        ->startOfDay();

                $endDate = $request->filled('end_date')
                    ? Carbon::parse(
                        $request->end_date,
                        $timezone
                    )->endOfDay()
                    : Carbon::now($timezone)
                        ->endOfDay();
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI TANGGAL
            |--------------------------------------------------------------------------
            */

            if ($startDate->gt($endDate)) {

                return response()->json([
                    'error' =>
                        'Tanggal mulai tidak boleh lebih besar dari tanggal berakhir.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | SALE DARI INVOICE
            | ==========================================================
            |
            | Transaksi utama ditentukan dari tanggal dibuatnya invoice.
            |
            | Contoh:
            |
            | Invoice:
            | 15 Agustus
            |
            | Pembayaran:
            | 05 September
            |
            | Jika filter September:
            | invoice tetap bisa tampil karena mendapatkan pembayaran
            | pada September.
            |
            |--------------------------------------------------------------------------
            */

            $invoiceSaleIds = Sale::query()
                ->whereBetween('created_at', [
                    $startDate,
                    $endDate
                ])
                ->where(function ($query) {

                    $query
                        ->whereNotNull('invoice')
                        ->orWhereNotNull('inv_manual');

                })
                ->pluck('id');

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | SALE DENGAN PEMBAYARAN PADA PERIODE
            | ==========================================================
            |
            | Debt hanya digunakan untuk mencari invoice lama yang
            | mendapatkan pembayaran pada periode filter.
            |
            | Debt TIDAK digunakan sebagai dasar nilai invoice.
            |
            |--------------------------------------------------------------------------
            */

            $paymentSaleIds = Sale::query()
                ->whereHas('debt', function ($query) use (
                    $startDate,
                    $endDate
                ) {

                    $query
                        ->whereNotNull('date_pay')
                        ->whereBetween('date_pay', [
                            $startDate,
                            $endDate
                        ]);

                })
                ->where(function ($query) {

                    $query
                        ->whereNotNull('invoice')
                        ->orWhereNotNull('inv_manual');

                })
                ->pluck('id');

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | GABUNGKAN SALE
            | ==========================================================
            */

            $saleIds = $invoiceSaleIds
                ->merge($paymentSaleIds)
                ->unique()
                ->values();

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | JIKA TIDAK ADA DATA
            | ==========================================================
            */

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

                    'start_date' =>
                        $startDate->format('Y-m-d'),

                    'end_date' =>
                        $endDate->format('Y-m-d'),

                    'total_transaction' => 0,

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | AMBIL SALE
            | ==========================================================
            */

            $sales = Sale::with([
                'customer',
                'debt.bank',
            ])
                ->whereIn('id', $saleIds)
                ->orderBy('created_at', 'desc')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | TOTAL
            | ==========================================================
            */

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

            /*
            |--------------------------------------------------------------------------
            | TOTAL CAPITAL PER SALE
            |--------------------------------------------------------------------------
            */

            $totalCapital = [];

            /*
            |--------------------------------------------------------------------------
            | REPORT
            |--------------------------------------------------------------------------
            */

            $report = [];

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | LOOP SALE
            | ==========================================================
            */

            foreach ($sales as $sale) {

                /*
                |--------------------------------------------------------------------------
                | CEK INVOICE BERADA DI PERIODE
                |--------------------------------------------------------------------------
                */

                $invoiceInPeriod = false;

                if ($sale->created_at) {

                    $invoiceDate = Carbon::parse(
                        $sale->created_at,
                        $timezone
                    );

                    $invoiceInPeriod =
                        $invoiceDate->between(
                            $startDate,
                            $endDate
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | ITEM SALES
                | ==========================================================
                |
                | Struktur item_sales:
                |
                | id
                | sale_id
                | divisi_id
                | itemcategory_id
                | name
                | no_seri
                | price
                | capital_price
                | price_bottom
                | region
                | date_in
                | status_return
                | created_at
                | updated_at
                | deleted_at
                |
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | ACCESSORIES SALES
                | ==========================================================
                */

                $accessoriesSales = AccessoriesSale::with([
                    'accessories'
                ])
                    ->where('sale_id', $sale->id)
                    ->whereNull('deleted_at')
                    ->where(function ($query) {

                        $query
                            ->whereNull('return_qty')
                            ->orWhereColumn(
                                'return_qty',
                                '<',
                                'qty'
                            );

                    })
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | TOTAL ITEM
                | ==========================================================
                */

                $totalItem = (int) (
                    $sale->total_item ?? 0
                );

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | MODAL ITEM
                | ==========================================================
                */

                $capitalItem =
                    $itemSales->sum(function ($item) {

                        return (float) (
                            $item->capital_price ?? 0
                        );

                    });

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | MODAL ACCESSORIES
                | ==========================================================
                */

                $capitalAcc = 0;

                foreach (
                    $accessoriesSales as $accessorySale
                ) {

                    $qty =
                        (float) (
                            $accessorySale->qty ?? 0
                        );

                    $returnQty =
                        (float) (
                            $accessorySale->return_qty ?? 0
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | QTY AKTIF
                    |--------------------------------------------------------------------------
                    */

                    $qtyTersisa =
                        max(
                            0,
                            $qty - $returnQty
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | HARGA MODAL
                    |--------------------------------------------------------------------------
                    */

                    $capitalPrice = 0;

                    if (
                        $accessorySale->accessories
                    ) {

                        $capitalPrice =
                            (float) (
                                $accessorySale
                                    ->accessories
                                    ->capital_price
                                ?? 0
                            );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL MODAL ACCESSORIES
                    |--------------------------------------------------------------------------
                    */

                    $capitalAcc +=
                        $qtyTersisa
                        * $capitalPrice;
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | TOTAL MODAL
                | ==========================================================
                */

                $capitalPrice =
                    $capitalItem
                    + $capitalAcc;

                /*
                |--------------------------------------------------------------------------
                | SIMPAN CAPITAL
                |--------------------------------------------------------------------------
                */

                $totalCapital[
                $sale->id
                ] = $capitalPrice;

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | NILAI SALES
                | ==========================================================
                */

                $totalPrice =
                    (float) (
                        $sale->total_price ?? 0
                    );

                $ppn =
                    (float) (
                        $sale->ppn ?? 0
                    );

                $pph =
                    (float) (
                        $sale->pph ?? 0
                    );

                $diskon =
                    (float) (
                        $sale->diskon ?? 0
                    );

                $ongkir =
                    (float) (
                        $sale->ongkir ?? 0
                    );

                $adminFee =
                    (float) (
                        $sale->admin_fee ?? 0
                    );

                $fee =
                    (float) (
                        $sale->fee ?? 0
                    );

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | PEMBAYARAN PADA PERIODE
                | ==========================================================
                */

                $periodPayments =
                    $sale->debt
                        ->filter(function ($payment) use (
                            $startDate,
                            $endDate,
                            $timezone
                        ) {

                            if (!$payment->date_pay) {
                                return false;
                            }

                            $datePay =
                                Carbon::parse(
                                    $payment->date_pay,
                                    $timezone
                                );

                            return $datePay->between(
                                $startDate,
                                $endDate
                            );
                        });

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | DITERIMA
                | ==========================================================
                */

                $diterima =
                    $periodPayments->sum(
                        function ($payment) {

                            return (float) (
                                $payment->pay_debts ?? 0
                            );

                        }
                    );

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | TOTAL PEMBAYARAN SAMPAI END DATE
                | ==========================================================
                */

                $paidUntilEndDate =
                    $sale->debt
                        ->filter(function ($payment) use (
                            $endDate,
                            $timezone
                        ) {

                            if (!$payment->date_pay) {
                                return false;
                            }

                            $datePay =
                                Carbon::parse(
                                    $payment->date_pay,
                                    $timezone
                                );

                            return $datePay->lte(
                                $endDate
                            );

                        })
                        ->sum(
                            function ($payment) {

                                return (float) (
                                    $payment->pay_debts ?? 0
                                );

                            }
                        );

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | TOTAL BAYAR
                | ==========================================================
                |
                | Untuk laporan periode:
                | jumlah pembayaran yang terjadi pada periode filter.
                |
                |--------------------------------------------------------------------------
                */

                $totalPay =
                    $diterima;

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | PIUTANG
                | ==========================================================
                |
                | Piutang dihitung berdasarkan:
                |
                | Total Invoice - seluruh pembayaran sampai end_date
                |
                |--------------------------------------------------------------------------
                */

                $piutang =
                    max(
                        0,
                        $totalPrice
                        - $paidUntilEndDate
                    );

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | INCOME
                |--------------------------------------------------------------------------
                |
                | Hanya invoice yang dibuat dalam periode yang menjadi
                | income bulan tersebut.
                |
                | Invoice bulan sebelumnya:
                | income = 0
                |
                |--------------------------------------------------------------------------
                */

                if ($invoiceInPeriod) {

                    $income =
                        $totalPrice;

                } else {

                    $income = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | LABA-RUGI
                |--------------------------------------------------------------------------
                |
                | Invoice bulan berjalan:
                |
                | Laba =
                | Total Invoice
                | - Modal Item
                | - Modal Accessories
                | - Fee
                |
                | Invoice bulan sebelumnya:
                |
                | Laba = 0
                |
                |--------------------------------------------------------------------------
                */

                if ($invoiceInPeriod) {

                    /*
                    |--------------------------------------------------------------------------
                    | FEE
                    |--------------------------------------------------------------------------
                    */

                    $feeForPeriod =
                        $fee;

                    /*
                    |--------------------------------------------------------------------------
                    | LABA
                    |--------------------------------------------------------------------------
                    */

                    $profit =
                        $totalPrice
                        - $capitalItem
                        - $capitalAcc
                        - $feeForPeriod;

                } else {

                    $feeForPeriod = 0;

                    $profit = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | DETAIL PAYMENT
                |--------------------------------------------------------------------------
                */

                $paymentDetails = [];

                foreach (
                    $periodPayments as $payment
                ) {

                    $bankName = '';

                    /*
                    |--------------------------------------------------------------------------
                    | BANK
                    |--------------------------------------------------------------------------
                    */

                    if ($payment->bank) {

                        $bankName =
                            $payment
                                ->bank
                                ->name;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK BANK NAME
                    |--------------------------------------------------------------------------
                    */

                    if (!$bankName) {

                        $bankName =
                            $payment->bank_name
                            ?? '';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DESCRIPTION
                    |--------------------------------------------------------------------------
                    */

                    $description =
                        $payment->description
                        ?? '';

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT METHOD
                    |--------------------------------------------------------------------------
                    */

                    if ($bankName) {

                        $paymentMethod =
                            $bankName;

                    } elseif ($description) {

                        $paymentMethod =
                            $description;

                    } else {

                        $paymentMethod =
                            'Cash';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT DATA
                    |--------------------------------------------------------------------------
                    */

                    $paymentDetails[] = [

                        'id' =>
                            $payment->id,

                        'date_pay' =>
                            $payment->date_pay,

                        'pay_debts' =>
                            (float) (
                                $payment->pay_debts
                                ?? 0
                            ),

                        'bank_name' =>
                            $paymentMethod,

                        'description' =>
                            $description,

                        'penerima' =>
                            $payment->penerima
                            ?? null,

                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | ITEM SALES DATA
                |--------------------------------------------------------------------------
                */

                $itemSalesData = [];

                foreach (
                    $itemSales as $itemSale
                ) {

                    $itemSalesData[] = [

                        'id' =>
                            $itemSale->id,

                        'sale_id' =>
                            $itemSale->sale_id,

                        'name' =>
                            $itemSale->name
                            ?? '',

                        'no_seri' =>
                            $itemSale->no_seri
                            ?? '',

                        'price' =>
                            (float) (
                                $itemSale->price
                                ?? 0
                            ),

                        'capital_price' =>
                            (float) (
                                $itemSale->capital_price
                                ?? 0
                            ),

                        'price_bottom' =>
                            (float) (
                                $itemSale->price_bottom
                                ?? 0
                            ),

                        'region' =>
                            $itemSale->region
                            ?? '',

                        'date_in' =>
                            $itemSale->date_in
                            ?? null,

                        'status_return' =>
                            $itemSale->status_return,

                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | ACCESSORIES DATA
                | ==========================================================
                */

                $accessoriesData = [];

                foreach (
                    $accessoriesSales as $accessorySale
                ) {

                    $qty =
                        (float) (
                            $accessorySale->qty
                            ?? 0
                        );

                    $returnQty =
                        (float) (
                            $accessorySale->return_qty
                            ?? 0
                        );

                    $qtyTersisa =
                        max(
                            0,
                            $qty - $returnQty
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | ACCESSORY NAME
                    |--------------------------------------------------------------------------
                    */

                    $accessoryName = '';

                    /*
                    |--------------------------------------------------------------------------
                    | CAPITAL PRICE
                    |--------------------------------------------------------------------------
                    */

                    $accessoryCapitalPrice = 0;

                    if (
                        $accessorySale->accessories
                    ) {

                        $accessoryName =
                            $accessorySale
                                ->accessories
                                ->name
                            ?? '';

                        $accessoryCapitalPrice =
                            (float) (
                                $accessorySale
                                    ->accessories
                                    ->capital_price
                                ?? 0
                            );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK NAME
                    |--------------------------------------------------------------------------
                    */

                    if (!$accessoryName) {

                        $accessoryName =
                            $accessorySale
                                ->name
                            ?? '';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ACCESSORIES DATA
                    |--------------------------------------------------------------------------
                    */

                    $accessoriesData[] = [

                        'id' =>
                            $accessorySale->id,

                        'name' =>
                            $accessoryName,

                        'qty' =>
                            $qtyTersisa,

                        'price_sale' =>
                            (float) (
                                $accessorySale
                                    ->price_sale
                                ?? 0
                            ),

                        'capital_price' =>
                            $accessoryCapitalPrice,

                        'return_qty' =>
                            $returnQty,

                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | TOTAL FOOTER
                | ==========================================================
                |
                | PENTING:
                |
                | Hanya invoice yang dibuat dalam periode yang masuk
                | ke Total Invoice, PPN, PPH, Diskon, Ongkir, Admin,
                | Modal, Fee dan Laba.
                |
                | Pembayaran tetap masuk Diterima.
                |
                |--------------------------------------------------------------------------
                */

                if ($invoiceInPeriod) {

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL INVOICE
                    |--------------------------------------------------------------------------
                    */

                    $totalInvoice +=
                        $totalPrice;

                    /*
                    |--------------------------------------------------------------------------
                    | PPN
                    |--------------------------------------------------------------------------
                    */

                    $totalPPN +=
                        $ppn;

                    /*
                    |--------------------------------------------------------------------------
                    | PPH
                    |--------------------------------------------------------------------------
                    */

                    $totalPPH +=
                        $pph;

                    /*
                    |--------------------------------------------------------------------------
                    | DISKON
                    |--------------------------------------------------------------------------
                    */

                    $totalDiskon +=
                        $diskon;

                    /*
                    |--------------------------------------------------------------------------
                    | ONGKIR
                    |--------------------------------------------------------------------------
                    */

                    $totalOngkir +=
                        $ongkir;

                    /*
                    |--------------------------------------------------------------------------
                    | ADMIN
                    |--------------------------------------------------------------------------
                    */

                    $totalAdmin +=
                        $adminFee;

                    /*
                    |--------------------------------------------------------------------------
                    | INCOME
                    |--------------------------------------------------------------------------
                    */

                    $totalIncome +=
                        $income;

                    /*
                    |--------------------------------------------------------------------------
                    | MODAL ITEM
                    |--------------------------------------------------------------------------
                    */

                    $totalCapitalItem +=
                        $capitalItem;

                    /*
                    |--------------------------------------------------------------------------
                    | MODAL ACCESSORIES
                    |--------------------------------------------------------------------------
                    */

                    $totalCapitalAcc +=
                        $capitalAcc;

                    /*
                    |--------------------------------------------------------------------------
                    | FEE
                    |--------------------------------------------------------------------------
                    */

                    $totalFee +=
                        $feeForPeriod;

                    /*
                    |--------------------------------------------------------------------------
                    | LABA
                    |--------------------------------------------------------------------------
                    */

                    $totalLaba +=
                        $profit;

                    /*
                    |--------------------------------------------------------------------------
                    | PIUTANG
                    |--------------------------------------------------------------------------
                    */

                    $totalPiutang +=
                        $piutang;
                }

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | PEMBAYARAN PERIODE
                |--------------------------------------------------------------------------
                */

                $totalDiterima +=
                    $diterima;

                $totalBayar +=
                    $totalPay;

                /*
                |--------------------------------------------------------------------------
                | ==========================================================
                | REPORT ROW
                |--------------------------------------------------------------------------
                */

                $report[] = [

                    /*
                    |--------------------------------------------------------------------------
                    | ID
                    |--------------------------------------------------------------------------
                    */

                    'id' =>
                        $sale->id,

                    /*
                    |--------------------------------------------------------------------------
                    | CREATED AT
                    |--------------------------------------------------------------------------
                    */

                    'created_at' =>
                        $sale->created_at,

                    /*
                    |--------------------------------------------------------------------------
                    | INVOICE
                    |--------------------------------------------------------------------------
                    */

                    'invoice' =>
                        $sale->invoice,

                    'inv_manual' =>
                        $sale->inv_manual,

                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOMER
                    |--------------------------------------------------------------------------
                    */

                    'customer' =>
                        $sale->customer,

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM
                    |--------------------------------------------------------------------------
                    */

                    'itemSales' =>
                        $itemSalesData,

                    /*
                    |--------------------------------------------------------------------------
                    | ACCESSORIES
                    |--------------------------------------------------------------------------
                    */

                    'accessories' =>
                        $accessoriesData,

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL ITEM
                    |--------------------------------------------------------------------------
                    */

                    'total_item' =>
                        $totalItem,

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL INVOICE
                    |--------------------------------------------------------------------------
                    */

                    'total_price' =>
                        $totalPrice,

                    /*
                    |--------------------------------------------------------------------------
                    | PPN
                    |--------------------------------------------------------------------------
                    */

                    'ppn' =>
                        $ppn,

                    /*
                    |--------------------------------------------------------------------------
                    | PPH
                    |--------------------------------------------------------------------------
                    */

                    'pph' =>
                        $pph,

                    /*
                    |--------------------------------------------------------------------------
                    | DISKON
                    |--------------------------------------------------------------------------
                    */

                    'diskon' =>
                        $diskon,

                    /*
                    |--------------------------------------------------------------------------
                    | ONGKIR
                    |--------------------------------------------------------------------------
                    */

                    'ongkir' =>
                        $ongkir,

                    /*
                    |--------------------------------------------------------------------------
                    | ADMIN
                    |--------------------------------------------------------------------------
                    */

                    'admin_fee' =>
                        $adminFee,

                    /*
                    |--------------------------------------------------------------------------
                    | DITERIMA
                    |--------------------------------------------------------------------------
                    |
                    | Pembayaran aktual pada periode.
                    |
                    |--------------------------------------------------------------------------
                    */

                    'nominal_in' =>
                        $nominalIn = $diterima,

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL BAYAR
                    |--------------------------------------------------------------------------
                    */

                    'pay' =>
                        $totalPay,

                    /*
                    |--------------------------------------------------------------------------
                    | PIUTANG
                    |--------------------------------------------------------------------------
                    */

                    'piutang' =>
                        $piutang,

                    /*
                    |--------------------------------------------------------------------------
                    | FEE
                    |--------------------------------------------------------------------------
                    */

                    'fee' =>
                        $fee,

                    /*
                    |--------------------------------------------------------------------------
                    | LABA
                    |--------------------------------------------------------------------------
                    */

                    'profit' =>
                        $profit,

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    'debt' =>
                        $paymentDetails,

                    /*
                    |--------------------------------------------------------------------------
                    | MODAL
                    |--------------------------------------------------------------------------
                    */

                    'capital_price' =>
                        $capitalPrice,

                    /*
                    |--------------------------------------------------------------------------
                    | PEMBAYARAN SAMPAI END DATE
                    |--------------------------------------------------------------------------
                    */

                    'paid_until_end_date' =>
                        $paidUntilEndDate,

                    /*
                    |--------------------------------------------------------------------------
                    | PEMBAYARAN PERIODE
                    |--------------------------------------------------------------------------
                    */

                    'diterima_period' =>
                        $diterima,

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS INVOICE
                    |--------------------------------------------------------------------------
                    */

                    'invoice_in_period' =>
                        $invoiceInPeriod,

                ];
            }

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | FOOTER
            | ==========================================================
            */

            $footer = [

                'total_invoice' =>
                    $totalInvoice,

                'ppn' =>
                    $totalPPN,

                'pph' =>
                    $totalPPH,

                'diskon' =>
                    $totalDiskon,

                'ongkir' =>
                    $totalOngkir,

                'admin' =>
                    $totalAdmin,

                'diterima' =>
                    $totalDiterima,

                'piutang' =>
                    $totalPiutang,

                'total_bayar' =>
                    $totalBayar,

                'fee' =>
                    $totalFee,

                'modal' =>
                    $totalCapitalItem
                    + $totalCapitalAcc,

                'laba' =>
                    $totalLaba,

            ];

            /*
            |--------------------------------------------------------------------------
            | ==========================================================
            | RESPONSE
            | ==========================================================
            */

            return response()->json([

                /*
                |--------------------------------------------------------------------------
                | REPORT
                |--------------------------------------------------------------------------
                */

                'report' =>
                    $report,

                /*
                |--------------------------------------------------------------------------
                | CAPITAL
                |--------------------------------------------------------------------------
                */

                'totalCapital' =>
                    $totalCapital,

                /*
                |--------------------------------------------------------------------------
                | TOTAL PRICE
                |--------------------------------------------------------------------------
                */

                'totalprice' =>
                    $totalInvoice,

                /*
                |--------------------------------------------------------------------------
                | INCOME
                |--------------------------------------------------------------------------
                */

                'income' =>
                    $totalIncome,

                /*
                |--------------------------------------------------------------------------
                | PPN
                |--------------------------------------------------------------------------
                */

                'ppn' =>
                    $totalPPN,

                /*
                |--------------------------------------------------------------------------
                | PPH
                |--------------------------------------------------------------------------
                */

                'pph' =>
                    $totalPPH,

                /*
                |--------------------------------------------------------------------------
                | DISKON
                |--------------------------------------------------------------------------
                */

                'diskon' =>
                    $totalDiskon,

                /*
                |--------------------------------------------------------------------------
                | ONGKIR
                |--------------------------------------------------------------------------
                */

                'ongkir' =>
                    $totalOngkir,

                /*
                |--------------------------------------------------------------------------
                | ADMIN
                |--------------------------------------------------------------------------
                */

                'admin' =>
                    $totalAdmin,

                /*
                |--------------------------------------------------------------------------
                | FEE
                |--------------------------------------------------------------------------
                */

                'fee' =>
                    $totalFee,

                /*
                |--------------------------------------------------------------------------
                | PROFIT
                |--------------------------------------------------------------------------
                */

                'profit' =>
                    $totalLaba,

                /*
                |--------------------------------------------------------------------------
                | FOOTER
                |--------------------------------------------------------------------------
                */

                'footer' =>
                    $footer,

                /*
                |--------------------------------------------------------------------------
                | FILTER
                |--------------------------------------------------------------------------
                */

                'start_date' =>
                    $startDate->format('Y-m-d'),

                'end_date' =>
                    $endDate->format('Y-m-d'),

                /*
                |--------------------------------------------------------------------------
                | JUMLAH TRANSAKSI
                |--------------------------------------------------------------------------
                */

                'total_transaction' =>
                    count($report),

            ]);

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'error' =>
                    true,

                'message' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine(),

            ], 500);
        }
    }
}
