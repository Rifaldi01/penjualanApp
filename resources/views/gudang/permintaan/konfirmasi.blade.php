@extends('layouts.master')
@section('title', 'DAFTAR PERMINTAAN AKSESORIS')
@section('content')
    <div class="card">
        <div class="container">
            <div class="card-header mb-3 mt-3">
                <form method="GET" class="mb-3">

                    <div class="row">

                        <div class="col-md-3">
                            <label>Bulan</label>

                            <select name="bulan" class="form-control bulan">

                                <option value="">-- Semua Bulan --</option>

                                @foreach([
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
                                    12 => 'Desember'
                                ] as $key => $value)

                                    <option value="{{ $key }}"
                                        {{ request('bulan') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Tahun</label>

                            <select name="tahun" class="form-control tahun">

                                <option value="">-- Semua Tahun --</option>

                                @for($i = now()->year; $i >= 2023; $i--)

                                    <option value="{{ $i }}"
                                        {{ request('tahun') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>

                                @endfor

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Divisi Tujuan</label>

                            <select name="divisi_tujuan" class="form-control divisi">

                                <option value="">-- Semua Divisi --</option>

                                @foreach($divisis as $divisi)

                                    <option value="{{ $divisi->id }}"
                                        {{ request('divisi_tujuan') == $divisi->id ? 'selected' : '' }}>
                                        {{ $divisi->name }}
                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">

                            <button class="btn btn-primary btn-sm me-2">
                                <i class="bx bx-filter"></i> Filter
                            </button>

                            <a href="{{ route('gudang.permintaan.konfirmasi') }}"
                               class="btn btn-danger btn-sm">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="konfir">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Tanggal</th>
                        <th>Kode Permintaan</th>
                        <th>Barang</th>
                        <th>Kode Barcode</th>
                        <th>Qty</th>
                        <th>Divisi Tujuan</th>
                        <th width="2%">Jumlah Barang</th>
                        <th width="2%">Status</th>
                        <th width="2%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permintaans as $key => $permintaan)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{ tanggal($permintaan->created_at) }}</td>
                            <td>{{ $permintaan->kode }}</td>
                            <td>
                                @forelse($permintaan->detailAccessories as $detail)
                                    {{ $detail->accessories?->name ?? '-' }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse($permintaan->detailAccessories as $detail)
                                    {{ $detail->accessories?->code_acces ?? '-' }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse($permintaan->detailAccessories as $accessory)
                                    <li>{{ $accessory->qty ?? '-' }}</li>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>{{ $permintaan->divisiTujuan->name }}</td>
                            <td>{{ $permintaan->jumlah }}</td>
                            <td>
                                @if($permintaan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($permintaan->status == 'disetujui')
                                    <span class="badge bg-success ">Disetujui</span>
                                @elseif($permintaan->status == 'diterima')
                                    <span class="badge bg-primary ">Diterima</span>
                                @elseif($permintaan->status == 'retur pending')
                                    <span class="badge bg-warning">Retur Pending</span>
                                @elseif($permintaan->status == 'retur')
                                    <span class="badge bg-danger">Retur</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleExtraLargeModal{{$permintaan->id}}"
                                        data-bs-tool="tooltip"
                                        data-bs-placement="top" title="Print Surat Jalan">
                                </button>
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_asal && $permintaan->status == 'pending')
                                    <!-- Tombol Terima -->
                                    <form action="{{ route('gudang.permintaan.receive', $permintaan->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-dnd btn-sm bx bx-check"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Setujui Permintaan"></button>
                                    </form>
                                @endif
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_tujuan && $permintaan->status == 'disetujui' || $permintaan->status == 'diterima' )
                                    {{--                                       <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"--}}
                                    {{--                                               data-bs-target="#exampleExtraLargeModal{{$permintaan->id}}" data-bs-tool="tooltip"--}}
                                    {{--                                               data-bs-placement="top" title="Print Surat Jalan">--}}
                                    {{--                                       </button>--}}

                                @endif
                                @include('gudang.permintaan.surat-penerimaan')
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_asal && $permintaan->status == 'retur pending')

                                    <form action="{{ route('gudang.permintaan.retur.approve',$permintaan->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <button class="btn btn-success btn-sm">
                                            Terima Retur
                                        </button>
                                    </form>

                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="" class="text-end">Total</th>
                        <th></th>
                        <th colspan=""></th>
                        <th colspan=""></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        if (!$.fn.DataTable.isDataTable('#konfir')) {

            function formatDatePdf() {
                const date = new Date();
                const day = String(date.getDate()).padStart(2, '0');
                const month = date.toLocaleString('en-US', {month: 'short'});
                const year = String(date.getFullYear()).slice(-2);
                return `${day} ${month} ${year}`;
            }

            var table2 = $('#konfir').DataTable({
                lengthChange: false,
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        filename: function () {
                            return 'Report Permintaan Accessories ' + formatDatePdf();
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        },
                        customize: function (doc) {

                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.pageMargins = [20, 20, 20, 20];

                            // Style header
                            doc.styles.tableHeader.alignment = 'center';
                            doc.styles.tableHeader.valignment = 'middle';

                            // Cari table node
                            var tableNode;

                            doc.content.forEach(function (item) {
                                if (item.table) {
                                    tableNode = item;
                                }
                            });

                            if (tableNode) {

                                tableNode.table.widths = [
                                    '3%', '12%', '16%', '28%', '10%', '7%', '10%', '8%', '8%'
                                ];

                                // CENTER semua isi body tabel
                                tableNode.table.body.forEach(function (row, rowIndex) {

                                    row.forEach(function (cell) {

                                        cell.alignment = 'center';
                                        cell.valignment = 'middle';

                                    });

                                });

                            }
                        }
                    },
                    {
                        extend: 'print',
                        footer: true,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                            stripHtml: false
                        },

                        customize: function (win) {

                            // Tambahkan style print
                            $(win.document.head).append(`
            <style>
                @page {
                    size: landscape;
                    margin: 10mm;
                }

                body {
                    font-size: 10px;
                }

                table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                }

                table th,
                table td {
                    text-align: center !important;
                    vertical-align: middle !important;
                    padding: 5px !important;
                    border: 1px solid #000 !important;
                }

                table thead th {
                    font-weight: bold;
                }
            </style>
        `);

                            // Center semua isi tabel
                            $(win.document.body).find('table tbody td').css({
                                'text-align': 'center',
                                'vertical-align': 'middle'
                            });

                            $(win.document.body).find('table thead th').css({
                                'text-align': 'center',
                                'vertical-align': 'middle'
                            });

                            $(win.document.body).find('table tfoot th').css({
                                'text-align': 'center',
                                'vertical-align': 'middle'
                            });

                        }
                    },{
                        extend: 'excel',
                        footer: true,
                        filename: function () {
                            return 'Report Permintaan Accessories ' + formatDatePdf();
                        },
                        exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]}
                    }
                ],

                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function (i) {
                        return typeof i === 'string'
                            ? i.replace(/[^0-9]/g, '') * 1
                            : typeof i === 'number'
                                ? i
                                : 0;
                    };

                    var totalQty = api
                        .column(7, {search: 'applied'})
                        .data()
                        .reduce(function (a, b) {
                            return a + intVal(b);
                        }, 0);

                    $(api.column(7).footer()).html(totalQty);
                }
            });

            table2.buttons().container()
                .appendTo('#konfir_wrapper .col-md-6:eq(0)');

            table2.on('order.dt search.dt', function () {
                let i = 1;
                table2.cells(null, 0, {search: 'applied', order: 'applied'})
                    .every(function () {
                        this.data(i++);
                    });
            }).draw();
        }
        $('.divisi').select2({
            theme: 'bootstrap-5', // Sesuaikan dengan tema CSS Anda
            placeholder: 'Pilih Divisi',
            allowClear: true,
        });
        $('.tahun').select2({
            theme: 'bootstrap-5', // Sesuaikan dengan tema CSS Anda
            placeholder: 'Pilih Tahun',
            allowClear: true,
        });
        $('.bulan').select2({
            theme: 'bootstrap-5', // Sesuaikan dengan tema CSS Anda
            placeholder: 'Pilih Bulan',
            allowClear: true,
        });
    </script>
@endpush
