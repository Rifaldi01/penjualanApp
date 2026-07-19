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
        $startDate = $request->start_date ?? '2026-04-14';
        $endDate   = $request->end_date ?? date('Y-m-d');
        $divisiId  = $request->divisi_id;

        if ($startDate < '2026-04-14') {
            $startDate = '2026-04-14';
        }

        $query = Accessories::whereHas('divisi', function ($q) {
            $q->where('status', 'active')
                ->where('name', '!=', 'Rental');
        });

        if (!empty($divisiId)) {
            $query->where('divisi_id', $divisiId);
        }

        $accessories = $query
            ->orderBy('name')
            ->get();

        $data = [];

        foreach ($accessories as $accessory) {

            $saldoAwal = $this->getSaldoAwal(
                $accessory,
                $accessory->divisi_id,
                $startDate
            );

            $barangMasuk = DB::table('accessories_ins')
                ->where('accessories_id', $accessory->id)
                ->where('status', 'buy')
                ->whereBetween('date_in', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            $barangTerjual = DB::table('accessories_sales')
                ->where('accessories_id', $accessory->id)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            $barangRetur = DB::table('sales_return_accessories')
                ->where('accessories_id', $accessory->id)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('qty');

            $barangRusak = DB::table('accessories_rejectes')
                ->where('code_acces', $accessory->code_acces)
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('stok');

            $permintaanKeluar = DB::table('detail_accessories as da')
                ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
                ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
                ->where('a.code_acces', $accessory->code_acces)
                ->where('p.divisi_id_asal', $accessory->divisi_id)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereBetween('p.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('da.qty');

            $permintaanMasuk = DB::table('detail_accessories as da')
                ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
                ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
                ->where('a.code_acces', $accessory->code_acces)
                ->where('p.divisi_id_tujuan', $accessory->divisi_id)
                ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
                ->whereBetween('p.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])
                ->sum('da.qty');

            $saldoAkhir =
                $saldoAwal +
                $barangMasuk +
                $barangRetur +
                $permintaanMasuk -
                $barangTerjual -
                $barangRusak -
                $permintaanKeluar;

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

        $data = collect($data)
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $divisis = Divisi::where('status', 'active')
            ->where('name', '!=', 'Rental')
            ->orderBy('name')
            ->get();
        return view(
            'manager.balance.index',
            compact(
                'data',
                'startDate',
                'endDate',
                'divisiId',
                'divisis'
            )
        );
    }

    private function getSaldoAwal($accessory, $divisiId, $startDate)
    {
        if ($startDate <= '2026-04-14') {
            return 0;
        }

        $saldoStart = '2026-04-14';

        $saldoEnd = date(
            'Y-m-d',
            strtotime($startDate . ' -1 day')
        );

        $masuk = DB::table('accessories_ins')
            ->where('accessories_id', $accessory->id)
            ->where('status', 'buy')
            ->whereBetween('date_in', [$saldoStart, $saldoEnd])
            ->sum('qty');

        $terjual = DB::table('accessories_sales')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('qty');

        $retur = DB::table('sales_return_accessories')
            ->where('accessories_id', $accessory->id)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('qty');

        $rusak = DB::table('accessories_rejectes')
            ->where('code_acces', $accessory->code_acces)
            ->whereBetween('created_at', [$saldoStart, $saldoEnd])
            ->sum('stok');

        $keluar = DB::table('detail_accessories as da')
            ->join('permintaans as p', 'p.id', '=', 'da.permintaan_id')
            ->join('accessories as a', 'a.id', '=', 'da.accessories_id')
            ->where('a.code_acces', $accessory->code_acces)
            ->where('p.divisi_id_asal', $divisiId)
            ->whereRaw("TRIM(LOWER(p.status)) = 'diterima'")
            ->whereBetween('p.created_at', [$saldoStart, $saldoEnd])
            ->sum('da.qty');

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
