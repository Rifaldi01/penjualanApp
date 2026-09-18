<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoriesBalanceController extends Controller
{
    public function index(Request $request)
    {
        $divisiId = auth()->user()->divisi_id;

        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */
        $startDate = $request->start_date ?? '2026-04-14';
        $endDate   = $request->end_date ?? date('Y-m-d');

        if ($startDate < '2026-04-14') {
            $startDate = '2026-04-14';
        }

        $accessories = Accessories::where('divisi_id', $divisiId)
            ->orderBy('name')
            ->get();

        $data = [];

        foreach ($accessories as $accessory) {

            /*
            |--------------------------------------------------------------------------
            | SALDO AWAL
            |--------------------------------------------------------------------------
            */
            $saldoAwal = $this->getSaldoAwal(
                $accessory,
                $divisiId,
                $startDate
            );

            /*
            |--------------------------------------------------------------------------
            | BARANG MASUK
            |--------------------------------------------------------------------------
            */
            $barangMasuk = DB::table('accessories_ins')
                ->where('accessories_id', $accessory->id)
                ->where('status', 'buy')
                ->whereBetween('date_in', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            /*
            |--------------------------------------------------------------------------
            | BARANG TERJUAL
            |--------------------------------------------------------------------------
            */
            $barangTerjual = DB::table('accessories_sales')
                ->where('accessories_id', $accessory->id)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            /*
            |--------------------------------------------------------------------------
            | BARANG RETUR
            |--------------------------------------------------------------------------
            */
            $barangRetur = DB::table('sales_return_accessories')
                ->where('accessories_id', $accessory->id)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            /*
            |--------------------------------------------------------------------------
            | BARANG RUSAK
            |--------------------------------------------------------------------------
            */
            $barangRusak = DB::table('accessories_rejectes')
                ->where('code_acces', $accessory->code_acces)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
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
                ->where('p.divisi_id_asal', $divisiId)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereBetween('p.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
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
                ->where('p.divisi_id_tujuan', $divisiId)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereBetween('p.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('da.qty');

            /*
            |--------------------------------------------------------------------------
            | SALDO AKHIR
            |--------------------------------------------------------------------------
            */
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
            | JANGAN TAMPILKAN JIKA SEMUA 0
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

        return view(
            'gudang.balance.index',
            compact(
                'data',
                'startDate',
                'endDate'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SALDO AWAL
    |--------------------------------------------------------------------------
    */
    private function getSaldoAwal($accessory, $divisiId, $startDate)
    {
        if ($startDate <= '2026-04-14') {
            return 0;
        }

        $saldoStart = '2026-04-14 00:00:00';

        $saldoEnd = date(
            'Y-m-d 23:59:59',
            strtotime($startDate . ' -1 day')
        );

        /*
        |--------------------------------------------------------------------------
        | BARANG MASUK
        |--------------------------------------------------------------------------
        */
        $masuk = DB::table('accessories_ins')
            ->where('accessories_id', $accessory->id)
            ->where('status', 'buy')
            ->whereBetween('date_in', [$saldoStart, $saldoEnd])
            ->sum('qty');

        /*
        |--------------------------------------------------------------------------
        | BARANG TERJUAL
        |--------------------------------------------------------------------------
        */
        $terjual = DB::table('accessories_sales')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('qty');

        /*
        |--------------------------------------------------------------------------
        | BARANG RETUR
        |--------------------------------------------------------------------------
        */
        $retur = DB::table('sales_return_accessories')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('qty');

        /*
        |--------------------------------------------------------------------------
        | BARANG RUSAK
        |--------------------------------------------------------------------------
        */
        $rusak = DB::table('accessories_rejectes')
            ->where('code_acces', $accessory->code_acces)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('stok');

        /*
        |--------------------------------------------------------------------------
        | PERMINTAAN KELUAR
        |--------------------------------------------------------------------------
        */
        $keluar = DB::table('detail_accessories as da')
            ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
            ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
            ->where('a.code_acces', $accessory->code_acces)
            ->where('p.divisi_id_asal', $divisiId)
            ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
            ->whereBetween('p.created_at', [$saldoStart, $saldoEnd])
            ->sum('da.qty');

        /*
        |--------------------------------------------------------------------------
        | PERMINTAAN MASUK
        |--------------------------------------------------------------------------
        */
        $masukDivisi = DB::table('detail_accessories as da')
            ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
            ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
            ->where('a.code_acces', $accessory->code_acces)
            ->where('p.divisi_id_tujuan', $divisiId)
            ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
            ->whereBetween('p.created_at', [$saldoStart, $saldoEnd])
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
