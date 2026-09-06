@extends('layouts.master')

@section('content')

    {{-- ========================================================= --}}
    {{-- WELCOME --}}
    {{-- ========================================================= --}}

    <div class="card col-12">
        <div class="card-body">

            <div class="text-center mt-3 mb-3">

                <h2>
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>

                <h3>
                    Anda Masuk Sebagai Admin
                </h3>

                <a
                    href="{{ route('admin.sale.create') }}"
                    class="btn btn-dnd mt-4"
                >
                    New Transaction
                </a>

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- FILTER CHART --}}
    {{-- ========================================================= --}}

    <div class="card radius-10">

        <div class="card-body">

            <div class="row align-items-end">

                {{-- PERIODE --}}
                <div class="col-md-3 mb-3">

                    <label class="form-label fw-bold">
                        Periode
                    </label>

                    <select
                        id="filterPeriode"
                        class="form-select"
                    >

                        <option value="bulan">
                            Bulan
                        </option>

                        <option value="tahun" selected>
                            Tahun
                        </option>

                    </select>

                </div>


                {{-- TAHUN --}}
                <div
                    class="col-md-2 mb-3"
                    id="wrapperTahun"
                >

                    <label class="form-label fw-bold">
                        Tahun
                    </label>

                    <select
                        id="filterTahun"
                        class="form-select"
                    >

                        @for($year = now()->year - 5; $year <= now()->year + 1; $year++)

                            <option
                                value="{{ $year }}"
                                {{ $year == now()->year ? 'selected' : '' }}
                            >
                                {{ $year }}
                            </option>

                        @endfor

                    </select>

                </div>


                {{-- BULAN --}}
                <div
                    class="col-md-2 mb-3 d-none"
                    id="wrapperBulan"
                >

                    <label class="form-label fw-bold">
                        Bulan
                    </label>

                    <select
                        id="filterBulan"
                        class="form-select"
                    >

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


                {{-- TANGGAL --}}
                <div
                    class="col-md-3 mb-3 d-none"
                    id="wrapperTanggal"
                >

                    <label class="form-label fw-bold">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        id="filterTanggal"
                        class="form-control"
                        value="{{ now()->format('Y-m-d') }}"
                    >

                </div>


                {{-- BUTTON --}}
                <div class="col-md-2 mb-3">

                    <button
                        type="button"
                        id="btnFilterChart"
                        class="btn btn-primary w-100"
                    >
                        <i class="bx bx-filter-alt"></i>
                        Filter
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">

        {{-- TRANSAKSI --}}
        <div class="col">

            <div
                class="card radius-10 border-start border-0 border-4 border-primary"
            >

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-0 text-secondary">
                                Transaksi
                            </p>

                            <h4
                                class="my-1 text-primary"
                                id="totalTransactions"
                            >
                                0
                            </h4>

                            <p class="mb-0 font-13">
                                Total transaksi
                            </p>

                        </div>

                        <div
                            class="widgets-icons-2 rounded-circle bg-gradient-deepblue text-white ms-auto"
                        >

                            <i class="bx bx-receipt"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ITEM --}}
        <div class="col">

            <div
                class="card radius-10 border-start border-0 border-4 border-danger"
            >

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-0 text-secondary">
                                Alat Terjual
                            </p>

                            <h4
                                class="my-1 text-danger"
                                id="totalItems"
                            >
                                0
                            </h4>

                            <p class="mb-0 font-13">
                                Total alat terjual
                            </p>

                        </div>

                        <div
                            class="widgets-icons-2 rounded-circle bg-gradient-danger text-white ms-auto"
                        >

                            <i class="bx bx-package"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ACCESSORIES --}}
        <div class="col">

            <div
                class="card radius-10 border-start border-0 border-4 border-warning"
            >

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div>

                            <p class="mb-0 text-secondary">
                                Aksesoris Terjual
                            </p>

                            <h4
                                class="my-1 text-warning"
                                id="totalAccessories"
                            >
                                0
                            </h4>

                            <p class="mb-0 font-13">
                                Total aksesoris terjual
                            </p>

                        </div>

                        <div
                            class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"
                        >

                            <i class="bx bx-box"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CHART --}}
    {{-- ========================================================= --}}

    <div class="card radius-10">

        <div class="card-body">

            <div
                class="d-flex align-items-center justify-content-between flex-wrap mb-3"
            >

                <div>

                    <h4 class="mb-1">
                        Aktivitas Penjualan
                    </h4>

                    <p
                        class="text-secondary mb-0"
                        id="chartDescription"
                    >
                        Menampilkan aktivitas transaksi berdasarkan bulan
                        Januari - Desember
                    </p>

                </div>

            </div>


            <div
                style="
                    position: relative;
                    width: 100%;
                    height: 430px;
                "
            >

                <canvas id="salesChart"></canvas>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PEMBAYARAN MENDEKATI JATUH TEMPO --}}
    {{-- ========================================================= --}}

    <div class="card col-12">

        <div class="card-body">

            <div class="text-center mt-3 mb-3">

                <h3>
                    Pembayaran Mendekati Jatuh Tempo
                </h3>

            </div>


            <div class="table-responsive">

                <table
                    id="termin"
                    class="table table-striped table-bordered"
                    style="width:100%"
                >

                    <thead>

                    <tr>

                        <th width="4%">
                            No
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Tenggang Waktu
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th
                            class="text-center"
                            width="7%"
                        >
                            Total Item
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Uang Masuk
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Total Harga
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Diskon
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Ongkir
                        </th>

                        <th
                            class="text-center"
                            width="8%"
                        >
                            Total Bayar
                        </th>

                        <th width="8%">
                            Kasir
                        </th>

                        <th
                            class="text-center"
                            width="5%"
                        >
                            Action
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @foreach($sales as $key => $data)

                        @if($data->nominal_in < $data->pay)

                            <tr>

                                <td>
                                    {{ $key + 1 }}
                                </td>

                                <td class="text-center">

                                    {{ dateId($data->deadlines) }}

                                </td>

                                <td>

                                    {{ $data->customer->name ?? '-' }}

                                </td>

                                <td class="text-center">

                                    {{ $data->total_item }}

                                </td>

                                <td>

                                    {{ formatRupiah($data->nominal_in) }}

                                </td>

                                <td>

                                    {{ formatRupiah($data->total_price) }}

                                </td>

                                <td>

                                    {{ formatRupiah($data->diskon) }}

                                </td>

                                <td>

                                    {{ formatRupiah($data->ongkir) }}

                                </td>

                                <td>

                                    {{ formatRupiah($data->pay) }}

                                </td>

                                <td>

                                    {{ $data->user->name ?? '-' }}

                                </td>

                                <td class="text-center">

                                    @if(!empty($data->customer?->phone_wa))

                                        <a
                                            href="https://api.whatsapp.com/send?phone=62{{ $data->customer->phone_wa }}&text=Halo%20Customer%20yth,%20segera%20selesaikan%20tagihan%20Pembelian-mu%20yang%20akan%20jatuh%20tempo%20pada%20{{ dateId($data->deadlines) }}"
                                            class="btn btn-success lni lni-whatsapp"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Chat Customer"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        ></a>

                                    @endif

                                </td>

                            </tr>

                        @endif

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection


@push('head')

    <style>

        .chart-wrapper {
            position: relative;
            width: 100%;
            height: 430px;
        }

        #salesChart {
            width: 100% !important;
            height: 100% !important;
        }

    </style>

@endpush


@push('js')

    {{-- ========================================================= --}}
    {{-- CHART JS --}}
    {{-- ========================================================= --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>

        $(document).ready(function () {

            /*
            |--------------------------------------------------------------------------
            | DATA AWAL DARI CONTROLLER
            |--------------------------------------------------------------------------
            */

            const initialChartData = @json($chartData);


            /*
            |--------------------------------------------------------------------------
            | CHART
            |--------------------------------------------------------------------------
            */

            let salesChart = null;


            /*
            |--------------------------------------------------------------------------
            | FORMAT ANGKA
            |--------------------------------------------------------------------------
            */

            function formatNumber(number) {

                return new Intl.NumberFormat(
                    'id-ID'
                ).format(number || 0);

            }


            /*
            |--------------------------------------------------------------------------
            | BUAT CHART
            |--------------------------------------------------------------------------
            */

            function renderChart(data) {

                const ctx = document
                    .getElementById('salesChart')
                    .getContext('2d');


                if (salesChart) {

                    salesChart.destroy();

                }


                salesChart = new Chart(
                    ctx,
                    {

                        type: 'line',

                        data: {

                            labels: data.labels,

                            datasets: [

                                {
                                    label: 'Transaksi',

                                    data: data.transactions,

                                    borderColor: '#2196F3',

                                    backgroundColor: 'rgba(33, 150, 243, 0.10)',

                                    borderWidth: 3,

                                    tension: 0.4,

                                    fill: false,

                                    pointRadius: 4,

                                    pointHoverRadius: 6
                                },


                                {
                                    label: 'Alat Terjual',

                                    data: data.items,

                                    borderColor: '#FF4560',

                                    backgroundColor: 'rgba(255, 69, 96, 0.10)',

                                    borderWidth: 3,

                                    tension: 0.4,

                                    fill: false,

                                    pointRadius: 4,

                                    pointHoverRadius: 6
                                },


                                {
                                    label: 'Aksesoris Terjual',

                                    data: data.accessories,

                                    borderColor: '#FFB000',

                                    backgroundColor: 'rgba(255, 176, 0, 0.10)',

                                    borderWidth: 3,

                                    tension: 0.4,

                                    fill: false,

                                    pointRadius: 4,

                                    pointHoverRadius: 6
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
                                                    context.raw
                                                );

                                        }

                                    }

                                }

                            },


                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        precision: 0,

                                        callback: function (value) {

                                            return formatNumber(
                                                value
                                            );

                                        }

                                    }

                                },

                                x: {

                                    grid: {

                                        display: false

                                    }

                                }

                            }

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE TOTAL
                |--------------------------------------------------------------------------
                */

                $('#totalTransactions').text(
                    formatNumber(
                        data.total?.transactions
                    )
                );

                $('#totalItems').text(
                    formatNumber(
                        data.total?.items
                    )
                );

                $('#totalAccessories').text(
                    formatNumber(
                        data.total?.accessories
                    )
                );

            }


            /*
            |--------------------------------------------------------------------------
            | DESKRIPSI CHART
            |--------------------------------------------------------------------------
            */

            function updateDescription(filter) {

                let text = '';

                if (filter === 'hari') {

                    text =
                        'Menampilkan aktivitas transaksi berdasarkan jam';

                } else if (filter === 'minggu') {

                    text =
                        'Menampilkan aktivitas transaksi Senin - Minggu';

                } else if (filter === 'bulan') {

                    text =
                        'Menampilkan aktivitas transaksi berdasarkan tanggal';

                } else {

                    text =
                        'Menampilkan aktivitas transaksi berdasarkan bulan Januari - Desember';

                }

                $('#chartDescription').text(text);

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD CHART
            |--------------------------------------------------------------------------
            */

            function loadChart() {

                const filter =
                    $('#filterPeriode').val();

                const tahun =
                    $('#filterTahun').val();

                const bulan =
                    $('#filterBulan').val();

                const tanggal =
                    $('#filterTanggal').val();


                $('#btnFilterChart')
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Loading...'
                    );


                $.ajax({

                    url: "{{ route('admin.dashboard.chart') }}",

                    type: 'GET',

                    data: {

                        filter: filter,

                        tahun: tahun,

                        bulan: bulan,

                        tanggal: tanggal

                    },


                    success: function (response) {

                        renderChart(response);

                        updateDescription(
                            filter
                        );

                    },


                    error: function (xhr) {

                        console.error(
                            xhr.responseText
                        );

                        alert(
                            'Gagal mengambil data chart.'
                        );

                    },


                    complete: function () {

                        $('#btnFilterChart')
                            .prop('disabled', false)
                            .html(
                                '<i class="bx bx-filter-alt"></i> Filter'
                            );

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | PERUBAHAN FILTER
            |--------------------------------------------------------------------------
            */

            $('#filterPeriode').on(
                'change',
                function () {

                    const filter = $(this).val();


                    /*
                    | Hari
                    */

                    if (filter === 'hari') {

                        $('#wrapperTahun')
                            .addClass('d-none');

                        $('#wrapperBulan')
                            .addClass('d-none');

                        $('#wrapperTanggal')
                            .removeClass('d-none');

                    }


                    /*
                    | Minggu
                    */

                    else if (filter === 'minggu') {

                        $('#wrapperTahun')
                            .addClass('d-none');

                        $('#wrapperBulan')
                            .addClass('d-none');

                        $('#wrapperTanggal')
                            .removeClass('d-none');

                    }


                    /*
                    | Bulan
                    */

                    else if (filter === 'bulan') {

                        $('#wrapperTahun')
                            .removeClass('d-none');

                        $('#wrapperBulan')
                            .removeClass('d-none');

                        $('#wrapperTanggal')
                            .addClass('d-none');

                    }


                    /*
                    | Tahun
                    */

                    else {

                        $('#wrapperTahun')
                            .removeClass('d-none');

                        $('#wrapperBulan')
                            .addClass('d-none');

                        $('#wrapperTanggal')
                            .addClass('d-none');

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON FILTER
            |--------------------------------------------------------------------------
            */

            $('#btnFilterChart').on(
                'click',
                function () {

                    loadChart();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CHART AWAL
            |--------------------------------------------------------------------------
            */

            renderChart(
                initialChartData
            );


            /*
            |--------------------------------------------------------------------------
            | DATATABLE TRANSAKSI
            |--------------------------------------------------------------------------
            */

            if ($.fn.DataTable) {

                const table =
                    $('#termin').DataTable({

                        pageLength: 10,

                        order: [
                            [1, 'asc']
                        ]

                    });


                table.on(
                    'order.dt search.dt',
                    function () {

                        let i = 1;

                        table
                            .cells(
                                null,
                                0,
                                {
                                    search: 'applied',
                                    order: 'applied'
                                }
                            )
                            .every(
                                function () {

                                    this.data(
                                        i++
                                    );

                                }
                            );

                    }
                ).draw();


                $('#userTable').DataTable({

                    pageLength: 10

                });

            }

        });

    </script>

@endpush
