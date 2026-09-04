<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Debt;
use App\Models\Sale;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CicilanController extends Controller
{
    /**
     * INDEX CICILAN MANAGER
     * Manager dapat melihat seluruh divisi.
     */
    public function index(Request $request)
    {

        $search = $request->input('search');
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan');
        $status = $request->input('status');
        $divisiId = $request->input('divisi_id');

        /*
        |--------------------------------------------------------------------------
        | DATA SALES
        |--------------------------------------------------------------------------
        */

        $sales = Sale::with([
            'customer',
            'divisi',
        ])
            ->withSum('debt', 'pay_debts')
            ->where('status_return', 0)

            // Filter tahun
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('created_at', $tahun);
            })

            // Filter bulan
            ->when($bulan, function ($query) use ($bulan) {
                $query->whereMonth('created_at', $bulan);
            })

            // Filter divisi
            ->when($divisiId, function ($query) use ($divisiId) {
                $query->where('divisi_id', $divisiId);
            })

            // Search
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('invoice', 'like', '%' . $search . '%')

                        ->orWhere(
                            'inv_manual',
                            'like',
                            '%' . $search . '%'
                        )

                        ->orWhereHas('customer', function ($customer) use ($search) {

                            $customer->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );

                        });

                });

            })

            ->orderByDesc('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        $sales = $sales->filter(function ($sale) use ($status) {

            $total = (float) ($sale->pay ?? 0);

            $sudahBayar = (float) (
                $sale->debt_sum_pay_debts ?? 0
            );

            $sisa = $total - $sudahBayar;


            if ($status === 'lunas') {

                return $sisa <= 0;

            }


            if ($status === 'belum_lunas') {

                return $sisa > 0;

            }


            return true;

        });


        /*
        |--------------------------------------------------------------------------
        | TOTAL PIUTANG
        |--------------------------------------------------------------------------
        */

        $totalPiutang = $sales->sum(function ($sale) {

            $total = (float) ($sale->pay ?? 0);

            $sudahBayar = (float) (
                $sale->debt_sum_pay_debts ?? 0
            );

            return max(
                0,
                $total - $sudahBayar
            );

        });


        /*
        |--------------------------------------------------------------------------
        | LIST TAHUN
        |--------------------------------------------------------------------------
        */

        $tahunList = Sale::where('status_return', 0)
            ->selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');


        /*
        |--------------------------------------------------------------------------
        | LIST DIVISI
        |--------------------------------------------------------------------------
        */

        $divisiList = Divisi::whereNotIn('name', ['Rental', 'rental'])
            ->where('status', 'active')
            ->get();


        return view(
            'manager.cicilan.index',
            compact(
                'sales',
                'search',
                'tahun',
                'bulan',
                'status',
                'divisiId',
                'tahunList',
                'divisiList',
                'totalPiutang'
            )
        );
    }


    /**
     * DETAIL CICILAN
     */
    public function show($id)
    {
        $sale = Sale::with([
            'customer',
            'divisi',
            'itemSales',
            'accessoriesSales.accessories',
            'debt.bank',
        ])->findOrFail($id);

        // Total seluruh pembayaran cicilan
        $totalBayar = (float) $sale->debt->sum('pay_debts');

        // Total tagihan
        $totalInvoice = (float) ($sale->pay ?? 0);

        // Sisa piutang
        $sisaPiutang = max(0, $totalInvoice - $totalBayar);

        // Data bank untuk pembayaran transfer
        $banks = Bank::orderBy('name')->get();

        return view('manager.cicilan.show', compact(
            'sale',
            'totalBayar',
            'totalInvoice',
            'sisaPiutang',
            'banks'
        ));
    }


    /**
     * PAYMENT CICILAN
     */
    public function payment(Request $request, $id)
    {
        $request->validate([
            'pay_debts' => [
                'required',
                'numeric',
                'min:1',
            ],

            'date_pay' => [
                'required',
                'date',
            ],

            'bank_id' => [
                'nullable',
                'exists:banks,id',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'penerima' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);


        DB::beginTransaction();

        try {

            $sale = Sale::lockForUpdate()
                ->findOrFail($id);


            $totalTagihan = (float) (
                $sale->pay ?? 0
            );


            $totalSudahBayar = (float) (
            Debt::where('sale_id', $sale->id)
                ->sum('pay_debts')
            );


            $sisaPiutang = max(
                0,
                $totalTagihan - $totalSudahBayar
            );


            $nominalBayar = (float) (
            $request->pay_debts
            );


            if ($nominalBayar <= 0) {

                return back()
                    ->with('error', 'Nominal pembayaran harus lebih dari 0.');

            }


            if ($nominalBayar > $sisaPiutang) {

                return back()
                    ->with(
                        'error',
                        'Nominal pembayaran melebihi sisa piutang.'
                    );

            }


            Debt::create([
                'sale_id' => $sale->id,
                'bank_id' => $request->bank_id,
                'pay_debts' => $nominalBayar,
                'penerima' => $request->penerima,
                'date_pay' => $request->date_pay,
                'description' => $request->description,
            ]);


            DB::commit();


            return back()
                ->with(
                    'success',
                    'Pembayaran cicilan berhasil disimpan.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();


            return back()
                ->with(
                    'error',
                    'Pembayaran gagal disimpan: ' . $e->getMessage()
                );

        }
    }


    /**
     * HAPUS PEMBAYARAN
     */
    public function destroyPayment($id)
    {
        DB::beginTransaction();

        try {

            $debt = Debt::findOrFail($id);

            $debt->delete();

            DB::commit();


            return back()
                ->with(
                    'success',
                    'Pembayaran berhasil dihapus.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();


            return back()
                ->with(
                    'error',
                    'Pembayaran gagal dihapus.'
                );

        }
    }
    public function updateSales(Request $request, $id)
    {
        $diskon = preg_replace(
            '/[^\d]/',
            '',
            $request->diskon
        );

        $biayaAdmin = preg_replace(
            '/[^\d]/',
            '',
            $request->admin_fee
        );

        $fee = preg_replace(
            '/[^\d]/',
            '',
            $request->fee
        );

        $request->merge([
            'diskon' => $diskon ?: 0,
            'admin_fee' => $biayaAdmin ?: 0,
            'fee' => $fee ?: 0,
        ]);

        $request->validate([
            'diskon' => [
                'required',
                'numeric',
                'min:0'
            ],

            'admin_fee' => [
                'required',
                'numeric',
                'min:0'
            ],

            'fee' => [
                'required',
                'numeric',
                'min:0'
            ],
        ]);

        DB::beginTransaction();

        try {

            $sale = Sale::lockForUpdate()
                ->where('status_return', 0)
                ->findOrFail($id);

            $diskonBaru = (float) $request->diskon;

            $biayaAdminBaru = (float)
            $request->admin_fee;

            $feeBaru = (float) $request->fee;

            $paySaatIni = (float) (
                $sale->pay ?? 0
            );

            $diskonLama = (float) (
                $sale->diskon ?? 0
            );

            $biayaAdminLama = (float) (
                $sale->admin_fee ?? 0
            );

            $payDasar =
                $paySaatIni +
                $diskonLama +
                $biayaAdminLama;

            $payBaru =
                $payDasar -
                $diskonBaru -
                $biayaAdminBaru;

            if ($payBaru < 0) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Diskon dan biaya admin tidak boleh lebih besar dari total transaksi.'
                    );
            }

            $totalBayar = (float) Debt::where(
                'sale_id',
                $sale->id
            )->sum('pay_debts');

            if ($payBaru < $totalBayar) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Pay baru Rp ' .
                        number_format(
                            $payBaru,
                            0,
                            ',',
                            '.'
                        ) .
                        ' lebih kecil dari total pembayaran yang sudah masuk Rp ' .
                        number_format(
                            $totalBayar,
                            0,
                            ',',
                            '.'
                        ) .
                        '.'
                    );
            }

            $sale->update([
                'pay' => $payBaru,
                'diskon' => $diskonBaru,
                'admin_fee' => $biayaAdminBaru,
                'fee' => $feeBaru,
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'manager.cicilan.show',
                    $sale->id
                )
                ->with(
                    'success',
                    'Diskon, biaya admin, fee, dan total pay berhasil diperbarui.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui transaksi: ' .
                    $e->getMessage()
                );
        }
    }

}
