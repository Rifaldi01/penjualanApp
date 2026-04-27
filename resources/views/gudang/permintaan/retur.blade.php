@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="container">
            <div class="card-header mb-3 mt-3">
                <h4>DAFTAR RETUR PERMINTAAN</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="retur">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Kode Acces</th>
                        <th>Qty</th>
                        <th>Divisi Tujuan</th>
                        <th width="2%">Jumlah Barang</th>
                        <th width="2%">Status</th>
                        <th width="2%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($returs as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{ tanggal($data->created_at) }}</td>
                            <td>{{ $data->kode }}</td>
                            <td>
                                @forelse($data->detailAccessories as $detail)
                                    {{ $detail->accessories?->name ?? '-' }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse($data->detailAccessories as $detail)
                                    {{ $detail->accessories?->code_acces ?? '-' }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse($data->detailAccessories as $accessory)
                                    <li>{{ $accessory->qty ?? '-' }}</li>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>{{ $data->divisiTujuan->name }}</td>
                            <td>{{ $data->jumlah }}</td>
                            <td>
                                @if($data->status == 'retur pending')
                                    <span class="badge bg-warning">Retur Pending</span>
                                @elseif($data->status == 'retur')
                                    <span class="badge bg-danger">Retur</span>
                                @endif
                            </td>
                            <td>
{{--                                <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"--}}
{{--                                        data-bs-target="#exampleExtraLargeModal{{$data->id}}"--}}
{{--                                        data-bs-tool="tooltip"--}}
{{--                                        data-bs-placement="top" title="Print Surat Jalan">--}}
{{--                                </button>--}}
{{--                                @include('gudang.permintaan.surat-retur')--}}
                                @if(Auth::user()->divisi_id == $data->divisi_id_asal && $data->status == 'retur pending')

                                    <form action="{{ route('gudang.permintaan.retur.approve',$data->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <button class="btn btn-success btn-sm bx bx-check"  data-bs-tool="tooltip"
                                                data-bs-placement="top" title="Terima Retur">
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
        <div class="container">
            <div class="card-header mb-3 mt-3">
                <h4>DAFTAR RETUR</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="returMinta">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Kode Acces</th>
                        <th>Qty</th>
                        <th>Asal Divisi</th>
                        <th width="2%">Jumlah Barang</th>
                        <th width="2%">Status</th>
                        <th width="2%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($returminta as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{ tanggal($data->created_at) }}</td>
                            <td>{{ $data->kode }}</td>
                            <td>
                                @foreach($data->detailAccessories as $accessory)
                                    @if($accessory->accessories)
                                        <li>{{ $accessory->accessories->name }}</li>
                                    @else
                                        <li>-</li> {{-- Bisa diganti placeholder sesuai kebutuhan --}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($data->detailAccessories as $accessory)
                                    @if($accessory->accessories)
                                        <li>{{ $accessory->accessories->code_acces }}</li>
                                    @else
                                        <li>-</li> {{-- Bisa diganti placeholder sesuai kebutuhan --}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($data->detailAccessories as $accessory)
                                    <li>{{ $accessory->qty }}</li>
                                @endforeach
                            </td>
                            <td>{{ $data->divisiAsal->name }}</td>
                            <td>{{ $data->jumlah }}</td>
                            <td>
                                @if($data->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($data->status == 'disetujui')
                                    <span class="badge bg-success ">Disetujui</span>
                                @elseif($data->status == 'diterima')
                                    <span class="badge bg-primary ">Diterima</span>
                                @elseif($data->status == 'retur pending')
                                    <span class="badge bg-warning">Retur Pending</span>
                                @elseif($data->status == 'retur')
                                    <span class="badge bg-danger">Returned</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->divisi_id == $data->divisi_id_tujuan && $data->status == 'retur')
                                <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleExtraLargeModal{{$data->id}}"
                                        data-bs-tool="tooltip"
                                        data-bs-placement="top" title="Print Surat Retur">
                                </button>
                                @endif
                                @include('gudang.permintaan.surat-retur')
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

            if (!$.fn.DataTable.isDataTable('#retur')) {

                function formatDatePdf() {
                    const date = new Date();
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = date.toLocaleString('en-US', {month: 'short'});
                    const year = String(date.getFullYear()).slice(-2);
                    return `${day} ${month} ${year}`;
                }

                var table2 = $('#retur').DataTable({
                    lengthChange: false,
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            footer: true,
                            filename: function () {
                                return 'Report Request Retur Accessories' + formatDatePdf();
                            },
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]},
                            customize: function (doc) {

                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.pageMargins = [20, 20, 20, 20];

                                // Cari table node secara aman
                                var tableNode;
                                doc.content.forEach(function (item) {
                                    if (item.table) {
                                        tableNode = item;
                                    }
                                });

                                if (tableNode) {
                                    tableNode.table.widths = [
                                        '5%', '20%', '30%', '20%', '5%', '15%', '5%'
                                    ];
                                }
                            }

                        },
                        {
                            extend: 'print',
                            footer: true,
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]}
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            filename: function () {
                                return 'Report Retur Request Accessories ' + formatDatePdf();
                            },
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]}
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
                    .appendTo('#retur_wrapper .col-md-6:eq(0)');

                table2.on('order.dt search.dt', function () {
                    let i = 1;
                    table2.cells(null, 0, {search: 'applied', order: 'applied'})
                        .every(function () {
                            this.data(i++);
                        });
                }).draw();
            }
        </script>
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
            if (!$.fn.DataTable.isDataTable('#returMinta')) {

                function formatDatePdf() {
                    const date = new Date();
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = date.toLocaleString('en-US', {month: 'short'});
                    const year = String(date.getFullYear()).slice(-2);
                    return `${day} ${month} ${year}`;
                }

                var table2 = $('#returMinta').DataTable({
                    lengthChange: false,
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            footer: true,
                            filename: function () {
                                return 'Report Retur Accessories' + formatDatePdf();
                            },
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]},
                            customize: function (doc) {

                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.pageMargins = [20, 20, 20, 20];

                                // Cari table node secara aman
                                var tableNode;
                                doc.content.forEach(function (item) {
                                    if (item.table) {
                                        tableNode = item;
                                    }
                                });

                                if (tableNode) {
                                    tableNode.table.widths = [
                                        '5%', '20%', '30%', '20%', '5%', '15%', '5%'
                                    ];
                                }
                            }

                        },
                        {
                            extend: 'print',
                            footer: true,
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]}
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            filename: function () {
                                return 'Report Retur Accessories ' + formatDatePdf();
                            },
                            exportOptions: {columns: [0, 1, 2, 3, 4, 5, 6]}
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
                    .appendTo('#returMinta_wrapper .col-md-6:eq(0)');

                table2.on('order.dt search.dt', function () {
                    let i = 1;
                    table2.cells(null, 0, {search: 'applied', order: 'applied'})
                        .every(function () {
                            this.data(i++);
                        });
                }).draw();
            }
        </script>
        <script>
            document.querySelectorAll('.approve-form').forEach(form => {

                form.addEventListener('submit', function (e) {

                    let btn = form.querySelector('.btnApprove');

                    if (btn.disabled) {
                        e.preventDefault();
                        return false;
                    }

                    btn.disabled = true;
                    btn.innerHTML = '';
                    btn.classList.remove('bx-check');
                    btn.innerText = '...';

                });

            });
        </script>
        <script>
            /* ======================================================
               GLOBAL SINGLE SUBMIT + CONFIRM ALL FORMS
            ====================================================== */

            document.addEventListener('DOMContentLoaded', function () {

                const forms = document.querySelectorAll('form');

                forms.forEach(function(form){

                    form.addEventListener('submit', function(e){

                        /* jika sedang proses */
                        if(form.dataset.submitted === 'true'){
                            e.preventDefault();
                            return false;
                        }

                        /* ambil tombol submit */
                        let btn = form.querySelector('button[type="submit"], button:not([type])');

                        let title = btn?.getAttribute('title') || btn?.innerText || 'Proses Data';

                        e.preventDefault();

                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Yakin ingin ' + title + '?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Batal'
                        }).then((result)=>{

                            if(result.isConfirmed){

                                form.dataset.submitted = 'true';

                                if(btn){
                                    btn.disabled = true;
                                    btn.innerHTML = '...';
                                }

                                form.submit();
                            }

                        });

                    });

                });

            });
        </script>


        <script>
            /* ======================================================
               DELETE BUTTON
            ====================================================== */

            document.querySelectorAll('.btn-delete').forEach(button => {

                button.addEventListener('click', function () {

                    let id = this.getAttribute('data-id');

                    if(this.dataset.clicked === 'true'){
                        return false;
                    }

                    Swal.fire({
                        title: 'Yakin ingin membatalkan?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            this.dataset.clicked = 'true';
                            this.disabled = true;
                            this.innerHTML = '...';

                            document.getElementById('delete-form-' + id).submit();
                        }

                    });

                });

            });
        </script>
    @endpush

@endpush
