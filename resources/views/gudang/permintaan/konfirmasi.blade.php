@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="container">
            <div class="card-header mb-3 mt-3">
                <h4>DAFTAR PERMINTAAN</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="konfir">
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
                            return 'Report Accessories ' + formatDatePdf();
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
    </script>
@endpush
