@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="container">
            <div class="card-header mt-3 mb-3">
                <div class="row">
                    <div class="col-sm">
                        <h4>DAFTAR MEMINTA</h4>
                    </div>
                    <div class="col-sm">
                        <div class="float-end">
                            <a href="{{route('gudang.permintaan.create')}}" class="btn btn-dnd bx bx-file" data-bs-toggle="tooltip" data-bs-placement="top" title="Meminta Accessories"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="konfir">
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
                    @foreach($permintaans as $key => $permintaan)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{ tanggal($permintaan->created_at) }}</td>
                            <td>{{ $permintaan->kode }}</td>
                            <td>
                                @foreach($permintaan->detailAccessories as $accessory)
                                    @if($accessory->accessories)
                                        <li>{{ $accessory->accessories->name }}</li>
                                    @else
                                        <li>-</li> {{-- Bisa diganti placeholder sesuai kebutuhan --}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($permintaan->detailAccessories as $accessory)
                                    @if($accessory->accessories)
                                        <li>{{ $accessory->accessories->code_acces }}</li>
                                    @else
                                        <li>-</li> {{-- Bisa diganti placeholder sesuai kebutuhan --}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($permintaan->detailAccessories as $accessory)
                                    <li>{{ $accessory->qty }}</li>
                                @endforeach
                            </td>
                            <td>{{ $permintaan->divisiAsal->name }}</td>
                            <td>{{ $permintaan->jumlah }}</td>
                            <td>
                                @if($permintaan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($permintaan->status == 'disetujui')
                                    <span class="badge bg-success ">Disetujui</span>
                                @else
                                    <span class="badge bg-primary ">Diterima</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_tujuan && $permintaan->status == 'disetujui')
                                    <!-- Tombol Setujui -->
                                    <form action="{{ route('gudang.permintaan.approve', $permintaan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm " data-bs-toggle="tooltip" data-bs-placement="top" title="Diterima">Diterima</button>
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
                const day   = String(date.getDate()).padStart(2, '0');
                const month = date.toLocaleString('en-US', { month: 'short' });
                const year  = String(date.getFullYear()).slice(-2);
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
                        exportOptions: { columns: [0,1,2,3,4,5,6] },
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
                        exportOptions: { columns: [0,1,2,3,4,5,6] }
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
                        .column(7, { search: 'applied' })
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
                table2.cells(null, 0, { search: 'applied', order: 'applied' })
                    .every(function () {
                        this.data(i++);
                    });
            }).draw();
        }
    </script>
@endpush

