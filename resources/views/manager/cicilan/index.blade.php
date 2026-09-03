@extends('layouts.master')

@section('title', 'Data Cicilan Manager')

@section('content')

    @php
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    <div class="card border-0 border-start border-bottom border-5 radius-15 border-primary">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        <i class="bx bx-money me-2"></i>
                        Data Cicilan
                    </h5>

                    <small class="text-muted">
                        Monitoring piutang seluruh divisi
                    </small>

                </div>

            </div>

        </div>


        <div class="card-body">

            {{-- =========================================================
                FILTER
            ========================================================== --}}
            <form
                action="{{ route('manager.cicilan.index') }}"
                method="GET">

                <div class="row g-3">

                    {{-- SEARCH --}}
                    <div class="col-md-3">

                        <label class="form-label fw-bold">
                            Pencarian
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                <i class="bx bx-search"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ $search ?? '' }}"
                                placeholder="Invoice / Invoice Manual / Customer">

                        </div>

                    </div>


                    {{-- TAHUN --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            Tahun
                        </label>

                        <select
                            name="tahun"
                            class="form-select">

                            <option value="">
                                Semua Tahun
                            </option>

                            @foreach ($tahunList as $itemTahun)

                                <option
                                    value="{{ $itemTahun }}"
                                    {{ (string) ($tahun ?? '') === (string) $itemTahun ? 'selected' : '' }}>

                                    {{ $itemTahun }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BULAN --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            Bulan
                        </label>

                        <select
                            name="bulan"
                            class="form-select">

                            <option value="">
                                Semua Bulan
                            </option>

                            @foreach ($namaBulan as $nomorBulan => $nama)

                                <option
                                    value="{{ $nomorBulan }}"
                                    {{ (string) ($bulan ?? '') === (string) $nomorBulan ? 'selected' : '' }}>

                                    {{ $nama }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DIVISI --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            Divisi
                        </label>

                        <select
                            name="divisi_id"
                            class="form-select">

                            <option value="">
                                Semua Divisi
                            </option>

                            @foreach ($divisiList as $divisi)

                                <option
                                    value="{{ $divisi->id }}"
                                    {{ (string) ($divisiId ?? '') === (string) $divisi->id ? 'selected' : '' }}>

                                    {{ $divisi->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS --}}
                    <div class="col-md-2">

                        <label class="form-label fw-bold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="lunas"
                                {{ ($status ?? '') === 'lunas' ? 'selected' : '' }}>

                                Lunas

                            </option>

                            <option
                                value="belum_lunas"
                                {{ ($status ?? '') === 'belum_lunas' ? 'selected' : '' }}>

                                Belum Lunas

                            </option>

                        </select>

                    </div>


                    {{-- BUTTON --}}
                    <div class="col-md-1 d-flex align-items-end">

                        <div class="d-flex gap-2 w-100">

                            <button
                                type="submit"
                                class="btn btn-primary"
                                title="Filter">

                                <i class="bx bx-filter-alt"></i>

                            </button>

                            <a
                                href="{{ route('manager.cicilan.index') }}"
                                class="btn btn-outline-secondary"
                                title="Reset Filter">

                                <i class="bx bx-reset"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </form>


            {{-- =========================================================
                FILTER AKTIF
            ========================================================== --}}
            @if (
                !empty($search) ||
                !empty($tahun) ||
                !empty($bulan) ||
                !empty($status) ||
                !empty($divisiId)
            )

                <div class="mt-3">

                    <div class="alert alert-light border mb-0">

                        <div class="d-flex align-items-center flex-wrap gap-2">

                            <strong>
                                <i class="bx bx-filter-alt me-1"></i>
                                Filter aktif:
                            </strong>


                            @if (!empty($search))

                                <span class="badge bg-primary">

                                    Pencarian:
                                    {{ $search }}

                                </span>

                            @endif


                            @if (!empty($tahun))

                                <span class="badge bg-info text-dark">

                                    Tahun:
                                    {{ $tahun }}

                                </span>

                            @endif


                            @if (!empty($bulan))

                                <span class="badge bg-info text-dark">

                                    Bulan:
                                    {{ $namaBulan[(int) $bulan] ?? $bulan }}

                                </span>

                            @endif


                            @if (!empty($divisiId))

                                @php
                                    $selectedDivisi = $divisiList->firstWhere(
                                        'id',
                                        $divisiId
                                    );
                                @endphp

                                <span class="badge bg-primary">

                                    Divisi:
                                    {{ $selectedDivisi->name ?? '-' }}

                                </span>

                            @endif


                            @if (($status ?? '') === 'lunas')

                                <span class="badge bg-success">

                                    Status: Lunas

                                </span>

                            @elseif (($status ?? '') === 'belum_lunas')

                                <span class="badge bg-warning text-dark">

                                    Status: Belum Lunas

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @endif


            {{-- =========================================================
                EXPORT
            ========================================================== --}}
            <div class="d-flex justify-content-end mt-3">

                <button
                    type="button"
                    id="exportExcel"
                    class="btn btn-success">

                    <i class="bx bx-spreadsheet me-1"></i>

                    Export Excel

                </button>

            </div>


            {{-- =========================================================
                TABLE
            ========================================================== --}}
            <div class="table-responsive mt-3">

                <table
                    id="table-cicilan"
                    class="table table-striped table-bordered align-middle"
                    style="width:100%">

                    <thead class="table-light">

                    <tr>

                        <th
                            width="50"
                            class="text-center">

                            No

                        </th>

                        <th
                            width="110">

                            Tanggal

                        </th>

                        <th>

                            Divisi

                        </th>

                        <th>

                            Invoice

                        </th>

                        <th>

                            Customer

                        </th>

                        <th class="text-end">

                            Total Tagihan

                        </th>

                        <th class="text-end">

                            Sudah Bayar

                        </th>

                        <th class="text-end">

                            Sisa Piutang

                        </th>

                        <th
                            width="120"
                            class="text-center">

                            Status

                        </th>

                        <th
                            width="80"
                            class="text-center">

                            Action

                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse ($sales as $sale)

                        @php

                            $totalTagihan =
                                (float) ($sale->pay ?? 0);

                            $sudahBayar =
                                (float) (
                                    $sale->debt_sum_pay_debts ?? 0
                                );

                            $sisaPiutang = max(
                                0,
                                $totalTagihan - $sudahBayar
                            );

                            $isLunas =
                                $sisaPiutang <= 0;

                        @endphp


                        <tr>

                            {{-- NO --}}
                            <td class="text-center">

                                {{ $loop->iteration }}

                            </td>


                            {{-- TANGGAL --}}
                            <td
                                data-order="{{ $sale->created_at?->timestamp ?? 0 }}">

                                {{ $sale->created_at?->format('d-m-Y') ?? '-' }}

                            </td>


                            {{-- DIVISI --}}
                            <td>

                                    <span class="badge bg-primary">

                                        {{ $sale->divisi->name ?? '-' }}

                                    </span>

                            </td>


                            {{-- INVOICE --}}
                            <td>

                                <div class="fw-bold">

                                    {{ $sale->invoice ?? '-' }}

                                </div>


                                @if (!empty($sale->inv_manual))

                                    <small class="text-muted">

                                        Manual:
                                        {{ $sale->inv_manual }}

                                    </small>

                                @endif

                            </td>


                            {{-- CUSTOMER --}}
                            <td>

                                @if ($sale->customer)

                                    <div class="fw-semibold">

                                        {{ $sale->customer->name }}

                                    </div>


                                    @if (!empty($sale->customer->phone_wa))

                                        <small class="text-muted">

                                            {{ $sale->customer->phone_wa }}

                                        </small>

                                    @elseif (!empty($sale->customer->phone))

                                        <small class="text-muted">

                                            {{ $sale->customer->phone }}

                                        </small>

                                    @endif

                                @else

                                    <span class="text-muted">
                                            -
                                        </span>

                                @endif

                            </td>


                            {{-- TOTAL TAGIHAN --}}
                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $totalTagihan,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- SUDAH BAYAR --}}
                            <td class="text-end">

                                Rp
                                {{ number_format(
                                    $sudahBayar,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- SISA PIUTANG --}}
                            <td class="text-end">

                                @if ($isLunas)

                                    <span class="text-success fw-bold">

                                            Rp 0

                                        </span>

                                @else

                                    <span class="text-danger fw-bold">

                                            Rp
                                            {{ number_format(
                                                $sisaPiutang,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="text-center">

                                @if ($isLunas)

                                    <span class="badge bg-success">

                                            <i class="bx bx-check-circle me-1"></i>

                                            Lunas

                                        </span>

                                @else

                                    <span class="badge bg-warning text-dark">

                                            <i class="bx bx-time-five me-1"></i>

                                            Belum Lunas

                                        </span>

                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td class="text-center">

                                <a
                                    href="{{ route(
                                            'manager.cicilan.show',
                                            $sale->id
                                        ) }}"
                                    class="btn btn-sm btn-primary bx bx-show"
                                    data-bs-tool="tooltip"
                                    data-bs-placement="top" title="Detail Cicilan">

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="bx bx-receipt"
                                        style="font-size:50px;">
                                    </i>

                                    <div class="mt-2">

                                        Belum ada data cicilan

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =========================================================
                TOTAL PIUTANG
            ========================================================== --}}
            @if (($status ?? '') === 'belum_lunas')

                <div class="row justify-content-end mt-3">

                    <div class="col-md-5 col-lg-4">

                        <div class="card border-0 shadow-sm mb-0">

                            <div class="card-body py-3">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <div class="fw-bold text-uppercase">

                                            TOTAL PIUTANG

                                        </div>

                                        <div class="text-muted small">

                                            Seluruh transaksi hasil filter

                                        </div>

                                    </div>


                                    <div class="text-end">

                                        <div class="fs-4 fw-bold text-danger">

                                            Rp
                                            {{ number_format(
                                                $totalPiutang,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

@endsection


@push('head')

    <style>

        #table-cicilan th {
            white-space: nowrap;
            vertical-align: middle;
        }

        #table-cicilan td {
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_length select {
            min-width: 70px;
            padding: 5px 30px 5px 10px;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 8px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 10px;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 5px;
        }

    </style>

@endpush


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#table-cicilan').DataTable({
                paging: true,
                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                order: [
                    [1, 'desc']
                ],

                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data cicilan',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',

                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: '›',
                        previous: '‹'
                    }
                }
            });


            // ==========================================
            // EXPORT EXCEL
            // ==========================================

            $('#exportExcel').on('click', function () {

                if (typeof XLSX === 'undefined') {
                    alert('Library Excel belum berhasil dimuat.');
                    return;
                }

                let data = [];


                // ==========================================
                // JUDUL
                // ==========================================

                data.push([
                    'LAPORAN CICILAN'
                ]);

                data.push([]);


                // ==========================================
                // HEADER
                // ==========================================

                data.push([
                    'No',
                    'Tanggal',
                    'Invoice',
                    'Customer',
                    'Total Tagihan',
                    'Sudah Bayar',
                    'Sisa Piutang',
                    'Status'
                ]);


                // ==========================================
                // SELURUH DATA HASIL FILTER
                // ==========================================

                @foreach ($sales as $sale)

                @php
                    $totalTagihanExcel = (float) ($sale->pay ?? 0);

                    $sudahBayarExcel = (float) (
                        $sale->debt_sum_pay_debts ?? 0
                    );

                    $sisaPiutangExcel = max(
                        0,
                        $totalTagihanExcel - $sudahBayarExcel
                    );

                    $isLunasExcel = $sisaPiutangExcel <= 0;
                @endphp

                data.push([
                    {{ $loop->iteration }},
                    '{{tanggal($sale->created_at)}}',
                    '{{ addslashes($sale->invoice ?? '-') }}',
                    '{{ addslashes($sale->customer->name ?? '-') }}',
                    {{ $totalTagihanExcel }},
                    {{ $sudahBayarExcel }},
                    {{ $sisaPiutangExcel }},
                    '{{ $isLunasExcel ? 'Lunas' : 'Belum Lunas' }}'
                ]);

                @endforeach


                // ==========================================
                // TOTAL PIUTANG
                // ==========================================

                data.push([]);

                data.push([
                    '',
                    '',
                    '',
                    '',
                    'TOTAL PIUTANG',
                    '',
                    '',
                    {{ $totalPiutang }},
                    ''
                ]);



                // ==========================================
                // BUAT WORKSHEET
                // ==========================================

                let worksheet = XLSX.utils.aoa_to_sheet(data);


                // ==========================================
                // LEBAR KOLOM
                // ==========================================

                worksheet['!cols'] = [
                    { wch: 6 },
                    { wch: 14 },
                    { wch: 22 },
                    { wch: 22 },
                    { wch: 30 },
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 18 }
                ];


                // ==========================================
                // WORKBOOK
                // ==========================================

                let workbook = XLSX.utils.book_new();

                XLSX.utils.book_append_sheet(
                    workbook,
                    worksheet,
                    'Cicilan'
                );


                // ==========================================
                // DOWNLOAD
                // ==========================================

                let fileName =
                    getFileName('Laporan Cicilan') + '.xlsx';

                XLSX.writeFile(
                    workbook,
                    fileName
                );

            });


            // ==========================================
            // NAMA FILE
            // ==========================================

            function getFileName(prefix) {

                let date = new Date();

                let tahun = date.getFullYear();

                let bulan = String(
                    date.getMonth() + 1
                ).padStart(2, '0');

                let tanggal = String(
                    date.getDate()
                ).padStart(2, '0');

                return prefix + '-' + tahun + bulan + tanggal;
            }

        });
    </script>
@endpush
