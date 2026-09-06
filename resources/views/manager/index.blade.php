@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1 fw-bold">
                Dashboard Manager
            </h4>

            <small class="text-muted">
                Monitoring aktivitas transaksi seluruh divisi aktif
            </small>
        </div>

    </div>

    {{-- =========================================================
    | FILTER
    ========================================================== --}}
    <div class="card border-0 shadow-sm radius-15 mb-4">

        <div class="card-body">

            <div class="row align-items-end">

                {{-- PERIODE --}}
                <div class="col-md-4 mb-3 mb-md-0">

                    <label for="filter" class="form-label fw-semibold">
                        Periode
                    </label>

                    <select id="filter"
                            class="form-select">
                        <option value="bulan">
                            Bulan
                        </option>

                        <option value="tahun" selected>
                            Tahun
                        </option>

                    </select>

                </div>


                {{-- TAHUN --}}
                <div class="col-md-3 mb-3 mb-md-0">

                    <label for="tahun" class="form-label fw-semibold">
                        Tahun
                    </label>

                    <select id="tahun"
                            class="form-select">

                        @for($year = now()->year; $year >= now()->year - 5; $year--)

                            <option value="{{ $year }}"
                                {{ $year == now()->year ? 'selected' : '' }}>

                                {{ $year }}

                            </option>

                        @endfor

                    </select>

                </div>


                {{-- BULAN --}}
                <div class="col-md-3 mb-3 mb-md-0"
                     id="bulanWrapper"
                     style="display: none;">

                    <label for="bulan" class="form-label fw-semibold">
                        Bulan
                    </label>

                    <select id="bulan"
                            class="form-select">

                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>

                    </select>

                </div>


                {{-- BUTTON FILTER --}}
                <div class="col-md-2">

                    <button type="button"
                            id="btnFilter"
                            class="btn btn-primary w-100">

                        <i class="bx bx-filter-alt me-1"></i>

                        Filter

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    | SUMMARY CARD
    ========================================================== --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">

        {{-- TRANSAKSI --}}
        <div class="col">

            <div class="card border-0 shadow-sm radius-15 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-1 text-muted">
                                Transaksi
                            </p>

                            <h3 class="mb-1 fw-bold text-primary"
                                id="totalTransactions">

                                0

                            </h3>

                            <small class="text-muted">
                                Total transaksi
                            </small>

                        </div>

                        <div class="widgets-icons-2 rounded-circle
                                    bg-primary text-white ms-auto">

                            <i class="bx bx-receipt"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ITEM SALES --}}
        <div class="col">

            <div class="card border-0 shadow-sm radius-15 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-1 text-muted">
                                Alat Terjual
                            </p>

                            <h3 class="mb-1 fw-bold text-danger"
                                id="totalItems">

                                0

                            </h3>

                            <small class="text-muted">
                                Total alat terjual
                            </small>

                        </div>

                        <div class="widgets-icons-2 rounded-circle
                                    bg-danger text-white ms-auto">

                            <i class="bx bx-package"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ACCESSORIES SALES --}}
        <div class="col">

            <div class="card border-0 shadow-sm radius-15 h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-1 text-muted">
                                Aksesoris Terjual
                            </p>

                            <h3 class="mb-1 fw-bold text-warning"
                                id="totalAccessories">

                                0

                            </h3>

                            <small class="text-muted">
                                Total aksesoris terjual
                            </small>

                        </div>

                        <div class="widgets-icons-2 rounded-circle
                                    bg-warning text-white ms-auto">

                            <i class="bx bx-cube"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    | CHART AKTIVITAS PENJUALAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm radius-15 mb-4">

        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between
                        align-items-start flex-wrap mb-4">

                <div>

                    <h4 class="fw-bold mb-1">
                        Aktivitas Penjualan
                    </h4>

                    <p class="text-muted mb-0"
                       id="chartDescription">

                        Menampilkan aktivitas transaksi
                        berdasarkan bulan Januari - Desember

                    </p>

                </div>

            </div>


            {{-- LOADING --}}
            <div id="chartLoading"
                 class="text-center py-5"
                 style="display: none;">

                <div class="spinner-border text-primary"
                     role="status">

                    <span class="visually-hidden">
                        Loading...
                    </span>

                </div>

                <div class="mt-2 text-muted">
                    Memuat data...
                </div>

            </div>


            {{-- CHART --}}
            <div id="chartContainer"
                 style="height: 430px;">

                <canvas id="salesChart"></canvas>

            </div>


            {{-- EMPTY --}}
            <div id="chartEmpty"
                 class="text-center py-5"
                 style="display: none;">

                <i class="bx bx-bar-chart-alt-2"
                   style="font-size: 50px; opacity: .3;">
                </i>

                <p class="text-muted mt-2 mb-0">
                    Tidak ada data transaksi pada periode ini.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
    | DIVISI
    ========================================================== --}}
    <div class="mb-3">

        <h4 class="fw-bold">
            Divisi
        </h4>

        <hr>

    </div>


    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">

        {{-- TOTAL DIVISI --}}
        <div class="col">

            <div class="card radius-10 border-start border-0 border-4 border-primary h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-0 text-secondary">
                                Total Divisi
                            </p>

                            <h4 class="my-1 text-primary">

                                {{ $totaldivisiactive }}
                                Aktif

                            </h4>

                            <p class="mb-0 font-13">

                                Dari {{ $totaldivisi }} divisi

                            </p>

                        </div>

                        <div class="widgets-icons-2
                                    rounded-circle
                                    bg-gradient-deepblue
                                    text-white ms-auto">

                            <i class="lni lni-apartment"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- DIVISI --}}
        @foreach ($divisi as $data)

            <div class="col">

                <div class="card radius-10 border-start border-0 border-4
                            {{ $data->status == 'active'
                                ? 'border-primary'
                                : 'border-danger' }}
                            h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div>

                                <p class="mb-0 text-secondary">

                                    {{ $data->name }}

                                </p>


                                @if($data->status == 'active')

                                    <h4 class="my-1 text-primary">

                                        <i class="bx bx-check-circle"></i>

                                        Aktif

                                    </h4>

                                @else

                                    <h4 class="my-1 text-danger">

                                        <i class="bx bx-x-circle"></i>

                                        Non-Aktif

                                    </h4>

                                @endif

                            </div>


                            <div class="widgets-icons-2
                                        rounded-circle
                                        {{ $data->status == 'active'
                                            ? 'bg-gradient-deepblue'
                                            : 'bg-danger' }}
                                        text-white ms-auto">

                                <i class="lni lni-apartment"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endforeach

    </div>


    {{-- =========================================================
    | DAFTAR STOK ALAT
    ========================================================== --}}
    <div class="mb-3">

        <h4 class="fw-bold">
            Daftar Stok Alat
        </h4>

        <hr>

    </div>


    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">

        {{-- TOTAL ITEM --}}
        <div class="col">

            <div class="card radius-10 border-start border-0
                        border-4 border-warning h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-0 text-secondary">
                                Total Alat
                            </p>

                            <h4 class="my-1 text-warning">
                                {{ $item }}
                            </h4>

                            <p class="mb-0 font-13">
                                Semua Alat
                            </p>

                        </div>

                        <div class="widgets-icons-2
                                    rounded-circle
                                    bg-gradient-orange
                                    text-white ms-auto">

                            <i class="bx bxs-box"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- CATEGORY --}}
        @foreach ($itemsByCategory as $itemCategory)

            <div class="col">

                <div class="card radius-10 border-start border-0
                            border-4 border-warning h-100">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div>

                                <p class="mb-0 text-secondary">

                                    {{ $itemCategory->cat->name ?? '-' }}

                                </p>

                                <h4 class="my-1 text-warning">

                                    {{ $itemCategory->total }}

                                </h4>

                            </div>


                            <div class="widgets-icons-2
                                        rounded-circle
                                        bg-gradient-orange
                                        text-white ms-auto">

                                <i class="bx bx-box"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endforeach

    </div>


    {{-- =========================================================
    | AKTIVITAS AKUN
    ========================================================== --}}
    <div class="card border-0 shadow-sm radius-15">

        <div class="card-body">

            <h4 class="text-uppercase fw-bold mb-4">
                Aktivitas Akun
            </h4>


            <div class="table-responsive">

                <table id="example"
                       class="table table-striped table-bordered"
                       style="width:100%">

                    <thead>

                    <tr>

                        <th width="2%">
                            No
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Role
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @foreach($user as $key => $data)

                        @foreach($data->roles as $role)

                            <tr>

                                <td>
                                    {{ $key + 1 }}
                                </td>

                                <td>
                                    {{ $data->name }}
                                </td>

                                <td>
                                    {{ $role->name }}
                                </td>

                                <td class="text-center user-status-{{ $data->id }}">

                                    @if($data->isOnline())

                                        <span class="badge bg-success">
                                            Online
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Offline
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection


{{-- =============================================================
| HEAD
============================================================= --}}
@push('head')

    <style>

        .radius-15 {
            border-radius: 15px !important;
        }

        #chartContainer {
            position: relative;
            width: 100%;
        }

        #salesChart {
            width: 100% !important;
            height: 100% !important;
        }

        .widgets-icons-2 {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        #chartLoading {
            min-height: 300px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .chart-legend-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

    </style>

@endpush


{{-- =============================================================
| JAVASCRIPT
============================================================= --}}
@push('js')

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        $(document).ready(function () {

            /*
            |--------------------------------------------------------------------------
            | VARIABLE
            |--------------------------------------------------------------------------
            */

            let salesChart = null;


            /*
            |--------------------------------------------------------------------------
            | FORMAT ANGKA
            |--------------------------------------------------------------------------
            */

            function formatNumber(number) {

                return new Intl.NumberFormat('id-ID')
                    .format(number || 0);

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE DESKRIPSI
            |--------------------------------------------------------------------------
            */

            function updateDescription(filter) {

                let description = '';

                if (filter === 'bulan') {

                    let bulan = $('#bulan option:selected').text();

                    let tahun = $('#tahun').val();

                    description =
                        'Menampilkan aktivitas transaksi tanggal 1 - akhir bulan ' +
                        bulan +
                        ' ' +
                        tahun;

                }

                else {

                    let tahun = $('#tahun').val();

                    description =
                        'Menampilkan aktivitas transaksi berdasarkan bulan Januari - Desember ' +
                        tahun;

                }

                $('#chartDescription').text(description);

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE FILTER
            |--------------------------------------------------------------------------
            */

            function updateFilter() {

                let filter = $('#filter').val();

                if (filter === 'bulan') {

                    $('#bulanWrapper').show();

                } else {

                    $('#bulanWrapper').hide();

                }

                updateDescription(filter);

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE CHART
            |--------------------------------------------------------------------------
            */

            function createChart(data) {

                const canvas =
                    document.getElementById('salesChart');

                const ctx =
                    canvas.getContext('2d');


                /*
                |--------------------------------------------------------------------------
                | HAPUS CHART LAMA
                |--------------------------------------------------------------------------
                */

                if (salesChart) {

                    salesChart.destroy();

                    salesChart = null;

                }


                /*
                |--------------------------------------------------------------------------
                | CEK DATA
                |--------------------------------------------------------------------------
                */

                let totalData =
                    (data.transactions || []).reduce(
                        (a, b) => a + Number(b || 0),
                        0
                    )
                    +
                    (data.items || []).reduce(
                        (a, b) => a + Number(b || 0),
                        0
                    )
                    +
                    (data.accessories || []).reduce(
                        (a, b) => a + Number(b || 0),
                        0
                    );


                /*
                |--------------------------------------------------------------------------
                | CHART
                |--------------------------------------------------------------------------
                */

                salesChart = new Chart(ctx, {

                    type: 'line',

                    data: {

                        labels: data.labels || [],

                        datasets: [

                            {
                                label: 'Transaksi',

                                data:
                                    data.transactions || [],

                                borderWidth: 3,

                                tension: 0.4,

                                fill: false,

                                pointRadius: 4,

                                pointHoverRadius: 7
                            },

                            {
                                label: 'Alat Terjual',

                                data:
                                    data.items || [],

                                borderWidth: 3,

                                tension: 0.4,

                                fill: false,

                                pointRadius: 4,

                                pointHoverRadius: 7
                            },

                            {
                                label: 'Aksesoris Terjual',

                                data:
                                    data.accessories || [],

                                borderWidth: 3,

                                tension: 0.4,

                                fill: false,

                                pointRadius: 4,

                                pointHoverRadius: 7
                            }

                        ]

                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        interaction: {

                            mode: 'index',

                            intersect: false

                        },

                        plugins: {

                            legend: {

                                display: true,

                                position: 'top',

                                align: 'end'

                            },

                            tooltip: {

                                callbacks: {

                                    label: function (context) {

                                        return context.dataset.label +
                                            ': ' +
                                            formatNumber(
                                                context.parsed.y
                                            );

                                    }

                                }

                            }

                        },

                        scales: {

                            x: {

                                grid: {

                                    display: false

                                },

                                ticks: {

                                    maxRotation: 0,

                                    autoSkip: true,

                                    maxTicksLimit: 31

                                }

                            },

                            y: {

                                beginAtZero: true,

                                ticks: {

                                    precision: 0,

                                    callback: function (value) {

                                        return formatNumber(value);

                                    }

                                }

                            }

                        }

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD CHART
            |--------------------------------------------------------------------------
            */

            function loadChart() {

                let filter =
                    $('#filter').val();

                let tahun =
                    $('#tahun').val();

                let bulan =
                    $('#bulan').val();


                /*
                |--------------------------------------------------------------------------
                | LOADING
                |--------------------------------------------------------------------------
                */

                $('#chartLoading')
                    .css('display', 'flex');

                $('#chartContainer')
                    .hide();

                $('#chartEmpty')
                    .hide();


                /*
                |--------------------------------------------------------------------------
                | AJAX
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url:
                        "{{ route('manager.dashboard.chart') }}",

                    type: 'GET',

                    data: {

                        filter: filter,

                        tahun: tahun,

                        bulan: bulan

                    },

                    dataType: 'json',

                    success: function (response) {

                        console.log(
                            'Dashboard Chart:',
                            response
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE TOTAL
                        |--------------------------------------------------------------------------
                        */

                        $('#totalTransactions')
                            .text(
                                formatNumber(
                                    response.total?.transactions ?? 0
                                )
                            );


                        $('#totalItems')
                            .text(
                                formatNumber(
                                    response.total?.items ?? 0
                                )
                            );


                        $('#totalAccessories')
                            .text(
                                formatNumber(
                                    response.total?.accessories ?? 0
                                )
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE DESCRIPTION
                        |--------------------------------------------------------------------------
                        */

                        updateDescription(
                            filter
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | CREATE CHART
                        |--------------------------------------------------------------------------
                        */

                        createChart(
                            response
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | SHOW CHART
                        |--------------------------------------------------------------------------
                        */

                        $('#chartLoading')
                            .hide();

                        $('#chartContainer')
                            .show();


                        /*
                        |--------------------------------------------------------------------------
                        | EMPTY
                        |--------------------------------------------------------------------------
                        */

                        let hasData = false;


                        if (
                            (response.transactions || [])
                                .some(value => Number(value) > 0)
                        ) {

                            hasData = true;

                        }


                        if (
                            (response.items || [])
                                .some(value => Number(value) > 0)
                        ) {

                            hasData = true;

                        }


                        if (
                            (response.accessories || [])
                                .some(value => Number(value) > 0)
                        ) {

                            hasData = true;

                        }


                        if (!hasData) {

                            $('#chartEmpty')
                                .show();

                        }

                    },

                    error: function (xhr) {

                        console.error(
                            'Dashboard Chart Error:',
                            xhr.responseText
                        );


                        $('#chartLoading')
                            .hide();

                        $('#chartContainer')
                            .show();


                        /*
                        |--------------------------------------------------------------------------
                        | RESET
                        |--------------------------------------------------------------------------
                        */

                        $('#totalTransactions')
                            .text('0');

                        $('#totalItems')
                            .text('0');

                        $('#totalAccessories')
                            .text('0');


                        $('#chartEmpty')
                            .show();

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | CHANGE PERIODE
            |--------------------------------------------------------------------------
            */

            $('#filter').on(
                'change',
                function () {

                    updateFilter();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CHANGE BULAN
            |--------------------------------------------------------------------------
            */

            $('#bulan').on(
                'change',
                function () {

                    updateDescription(
                        $('#filter').val()
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CHANGE TAHUN
            |--------------------------------------------------------------------------
            */

            $('#tahun').on(
                'change',
                function () {

                    updateDescription(
                        $('#filter').val()
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON FILTER
            |--------------------------------------------------------------------------
            */

            $('#btnFilter').on(
                'click',
                function () {

                    loadChart();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | INITIAL
            |--------------------------------------------------------------------------
            */

            updateFilter();

            loadChart();


            /*
            |--------------------------------------------------------------------------
            | USER STATUS REALTIME POLLING
            |--------------------------------------------------------------------------
            */

            setInterval(function () {

                $.ajax({

                    url:
                        "{{ route('manager.user.status') }}",

                    type: 'GET',

                    success: function (users) {

                        users.forEach(function (user) {

                            let html = '';

                            if (user.online) {

                                html =
                                    '<span class="badge bg-success">' +
                                    'Online' +
                                    '</span>';

                            } else {

                                html =
                                    '<span class="badge bg-danger">' +
                                    'Offline' +
                                    '</span>';

                            }


                            $('.user-status-' + user.id)
                                .html(html);

                        });

                    }

                });

            }, 3000);

        });

    </script>

@endpush
