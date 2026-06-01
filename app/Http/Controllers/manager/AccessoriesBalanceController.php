<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoriesBalanceController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('n');
        $divisiId = $request->divisi_id;

        /*
        |--------------------------------------------------------------------------
        | START SISTEM APRIL 2026
        |--------------------------------------------------------------------------
        */
        if ($year == 2026 && $month < 4) {
            $month = 4;
        }

        $query = Accessories::query();

        if (!empty($divisiId)) {
            $query->where('divisi_id', $divisiId);
        }

        $accessories = $query
            ->orderBy('name', 'asc')
            ->get();

        $data = [];

        foreach ($accessories as $accessory) {

            $saldoAwal = $this->getSaldoAwal(
                $accessory,
                $accessory->divisi_id,
                $year,
                $month
            );

            $barangMasuk = DB::table('accessories_ins')
                ->where('accessories_id', $accessory->id)
                ->whereYear('date_in', $year)
                ->whereMonth('date_in', $month)
                ->sum('qty');

            $barangTerjual = DB::table('accessories_sales')
                ->where('accessories_id', $accessory->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('qty');

            $barangRetur = DB::table('sales_return_accessories')
                ->where('accessories_id', $accessory->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('qty');

            $barangRusak = DB::table('accessories_rejectes')
                ->where('code_acces', $accessory->code_acces)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('stok');

            /*
            |--------------------------------------------------------------------------
            | PERMINTAAN KELUAR
            |--------------------------------------------------------------------------
            */
            $permintaanKeluar = DB::table('detail_accessories as da')
                ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
                ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
                ->where('a.code_acces', $accessory->code_acces)
                ->where('p.divisi_id_asal', $accessory->divisi_id)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereYear('p.created_at', $year)
                ->whereMonth('p.created_at', $month)
                ->sum('da.qty');

            /*
            |--------------------------------------------------------------------------
            | PERMINTAAN MASUK
            |--------------------------------------------------------------------------
            */
            $permintaanMasuk = DB::table('detail_accessories as da')
                ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
                ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
                ->where('a.code_acces', $accessory->code_acces)
                ->where('p.divisi_id_tujuan', $accessory->divisi_id)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereYear('p.created_at', $year)
                ->whereMonth('p.created_at', $month)
                ->sum('da.qty');

            $saldoAkhir =
                $saldoAwal +
                $barangMasuk +
                $barangRetur +
                $permintaanMasuk -
                $barangTerjual -
                $barangRusak -
                $permintaanKeluar;

            /*
            |--------------------------------------------------------------------------
            | HIDE SEMUA 0
            |--------------------------------------------------------------------------
            */
            if (
                $saldoAwal == 0 &&
                $barangMasuk == 0 &&
                $barangRetur == 0 &&
                $permintaanMasuk == 0 &&
                $barangTerjual == 0 &&
                $barangRusak == 0 &&
                $permintaanKeluar == 0 &&
                $saldoAkhir == 0
            ) {
                continue;
            }

            $data[] = (object)[
                'divisi_id'         => $accessory->divisi_id,
                'code'              => $accessory->code_acces,
                'name'              => $accessory->name,
                'saldo_awal'        => $saldoAwal,
                'barang_masuk'      => $barangMasuk,
                'barang_retur'      => $barangRetur,
                'permintaan_masuk'  => $permintaanMasuk,
                'barang_terjual'    => $barangTerjual,
                'barang_rusak'      => $barangRusak,
                'permintaan_keluar' => $permintaanKeluar,
                'saldo_akhir'       => $saldoAkhir,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | SORT A-Z SETELAH FILTER
        |--------------------------------------------------------------------------
        */
        $data = collect($data)
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $divisis = Divisi::orderBy('name')->get();

        return view(
            'manager.balance.index',
            compact(
                'data',
                'year',
                'month',
                'divisiId',
                'divisis'
            )
        );
    }

    private function getSaldoAwal($accessory, $divisiId, $year, $month)
    {
        if ($year == 2026 && $month == 4) {
            return 0;
        }

        $startDate = '2026-04-01';

        $endDate = date(
            'Y-m-t',
            strtotime($year . '-' . $month . '-01 -1 month')
        );

        $masuk = DB::table('accessories_ins')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('date_in', [$startDate, $endDate])
            ->sum('qty');

        $terjual = DB::table('accessories_sales')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('qty');

        $retur = DB::table('sales_return_accessories')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('qty');

        $rusak = DB::table('accessories_rejectes')
            ->where('code_acces', $accessory->code_acces)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('stok');

        $keluar = DB::table('detail_accessories as da')
            ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
            ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
            ->where('a.code_acces', $accessory->code_acces)
            ->where('p.divisi_id_asal', $divisiId)
            ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
            ->whereBetween('p.created_at', [$startDate, $endDate])
            ->sum('da.qty');

        $masukDivisi = DB::table('detail_accessories as da')
            ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
            ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
            ->where('a.code_acces', $accessory->code_acces)
            ->where('p.divisi_id_tujuan', $divisiId)
            ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
            ->whereBetween('p.created_at', [$startDate, $endDate])
            ->sum('da.qty');

        return
            $masuk +
            $retur +
            $masukDivisi -
            $terjual -
            $rusak -
            $keluar;
    }
}
