<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesBalance;
use App\Models\DetailAccessoriesBalance;
use App\Models\Permintaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccessoriesBalanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $divisiId = auth()->user()->divisi_id;
        $currentYear = now()->year;

        $firstYear = DB::table('accessories')
            ->where('divisi_id', $divisiId)
            ->selectRaw('MIN(YEAR(created_at)) as year')
            ->value('year') ?? $currentYear;

        for ($year = $firstYear; $year <= $currentYear; $year++) {

            $exists = AccessoriesBalance::where('divisi_id', $divisiId)
                ->where('year', $year)
                ->exists();

            if (!$exists) {
                $this->generateYearlyBalance($divisiId, $year);
            }
        }

        $balances = AccessoriesBalance::with('divisi')
            ->where('divisi_id', $divisiId)
            ->orderByDesc('year')
            ->get();

        return view('gudang.balance.index', compact('balances'));
    }
    public function data()
    {
        $divisiId = auth()->user()->divisi_id;

        $balances = AccessoriesBalance::with('divisi')
            ->where('divisi_id', $divisiId)
            ->orderByDesc('year')
            ->get();

        return response()->json(['data' => $balances]);
    }

    public function show($id)
    {
        $balance = AccessoriesBalance::with([
            'divisi',
            'details.accessory'
        ])->findOrFail($id);

        return view('gudang.balance.show', compact('balance'));
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE YEARLY BALANCE
    |--------------------------------------------------------------------------
    */
    private function generateYearlyBalance($divisiId, $year)
    {
        DB::transaction(function () use ($divisiId, $year) {

            $currentYear = now()->year;

            $previousYearBalance = AccessoriesBalance::where('divisi_id', $divisiId)
                ->where('year', $year - 1)
                ->value('remainder') ?? 0;

            $balance = AccessoriesBalance::create([
                'divisi_id'      => $divisiId,
                'year'           => $year,
                'capital_stock'  => $previousYearBalance,
                'accessories_in' => 0,
                'sale'           => 0,
                'reject'         => 0,
                'retur'          => 0,
                'request'        => 0,
                'request_in'     => 0,
                'remainder'      => 0,
            ]);
            $mintaKeluar = DB::table('permintaans')
                ->where('permintaans.divisi_id_asal', $divisiId)
                ->whereRaw('LOWER(permintaans.status) = ?', ['diterima'])
                ->whereYear('permintaans.created_at', $year)
                ->sum('permintaans.jumlah');
            $mintaMasuk = DB::table('permintaans')
                ->where('permintaans.divisi_id_tujuan', $divisiId)
                ->whereRaw('LOWER(permintaans.status) = ?', ['diterima'])
                ->whereYear('permintaans.created_at', $year)
                ->sum('permintaans.jumlah');
            $accessories = Accessories::where('divisi_id', $divisiId)->get();

            foreach ($accessories as $accessory) {

                $previousBalance = DetailAccessoriesBalance::whereHas('balance', function ($q) use ($divisiId, $year) {
                    $q->where('divisi_id', $divisiId)
                        ->where('year', $year - 1);
                })
                    ->where('accessories_id', $accessory->id)
                    ->value('accessories_balance') ?? 0;

                /*
                |--------------------------------------------------------------------------
                | TRANSAKSI MASUK
                |--------------------------------------------------------------------------
                */
                $in = DB::table('accessories_ins')
                    ->where('accessories_id', $accessory->id)
                    ->whereYear('created_at', $year)
                    ->sum('qty');

                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */
                $sale = DB::table('accessories_sales')
                    ->where('accessories_id', $accessory->id)
                    ->whereYear('created_at', $year)
                    ->sum('qty');

                /*
                |--------------------------------------------------------------------------
                | REJECT
                |--------------------------------------------------------------------------
                */
                $reject = DB::table('accessories_rejectes')
                    ->where('code_acces', $accessory->code_acces)
                    ->whereYear('created_at', $year)
                    ->sum('stok');

                /*
                |--------------------------------------------------------------------------
                | RETUR
                |--------------------------------------------------------------------------
                */
                $retur = DB::table('accessories_sales')
                    ->where('accessories_id', $accessory->id)
                    ->whereNotNull('deleted_at')
                    ->whereYear('deleted_at', $year)
                    ->sum('qty');

                /*
                |--------------------------------------------------------------------------
                | REQUEST OUT (KELUAR DARI DIVISI INI)
                |--------------------------------------------------------------------------
                */
                $requestOut = DB::table('detail_accessories')
                    ->join('permintaans', 'permintaans.id', '=', 'detail_accessories.permintaan_id')
                    ->where('detail_accessories.accessories_id', $accessory->id)
                    ->where('permintaans.divisi_id_asal', $divisiId) // <-- ini yang benar
                    ->whereRaw('LOWER(permintaans.status) = ?', ['diterima'])
                    ->whereYear('permintaans.created_at', $year)
                    ->sum('detail_accessories.qty');

                /*
                |--------------------------------------------------------------------------
                | REQUEST IN (MASUK KE DIVISI INI)
                |--------------------------------------------------------------------------
                */
                $requestIn = DB::table('detail_accessories')
                    ->join('permintaans', 'permintaans.id', '=', 'detail_accessories.permintaan_id')
                    ->where('permintaans.divisi_id_tujuan', $divisiId)
                    ->where('permintaans.status', 'diterima')
                    ->whereYear('permintaans.created_at', $year)
                    ->sum('detail_accessories.qty');
//                dd( DB::table('detail_accessories')
//                    ->join('permintaans', 'permintaans.id', '=', 'detail_accessories.permintaan_id')
//                    ->where('permintaans.divisi_id_tujuan', $divisiId)
//                    ->where('permintaans.status', 'diterima')
//                    ->whereYear('permintaans.created_at', $year)
//                    ->get());
                /*
                |--------------------------------------------------------------------------
                | HITUNG SALDO
                |--------------------------------------------------------------------------
                */
                $balanceQty =
                    $previousBalance
                    + $in
                    + $retur
                    - $sale
                    - $reject
                    - $requestOut;

                DetailAccessoriesBalance::create([
                    'accessories_id'            => $accessory->id,
                    'balance_accessories_id'    => $balance->id,
                    'accessories_capital_stock' => $previousBalance,
                    'accessories_in'            => $in,
                    'accessories_sale'          => $sale,
                    'accessories_reject'        => $reject,
                    'accessories_retur'         => $retur,
                    'accessories_requested'     => $requestOut,
                    'accessories_requested_in'  => $requestIn,
                    'accessories_balance'       => $balanceQty,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE MASTER
            |--------------------------------------------------------------------------
            */

            $updateData = [
                'capital_stock'  => $balance->details()->sum('accessories_capital_stock'),
                'accessories_in' => $balance->details()->sum('accessories_in'),
                'sale'           => $balance->details()->sum('accessories_sale'),
                'reject'         => $balance->details()->sum('accessories_reject'),
                'retur'          => $balance->details()->sum('accessories_retur'),
                'request'        => $mintaKeluar,
                'request_in'     => $mintaMasuk,
            ];

            if ($year < $currentYear) {
                $updateData['remainder'] =
                    $balance->details()->sum('accessories_capital_stock') +
                    $balance->details()->sum('accessories_in') +
                    $balance->details()->sum('accessories_retur') +
                    $mintaMasuk -
                    $mintaKeluar -
                    $balance->details()->sum('accessories_reject') -
                    $balance->details()->sum('accessories_sale');
            }

            $balance->update($updateData);
        });
    }
}
