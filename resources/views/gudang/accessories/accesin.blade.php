@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                @if(request()->routeIs('gudang.acces.accesin'))
                    <h4 class="text-uppercase">List Accessories In</h4>
                @else
                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="text-uppercase">List Accessories Out</h4>
                        </div>
                        <div class="col-sm-6">
                            <form id="filterForm">
                                <div class="float-end me-2">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Pilih Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option
                                                value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="float-end me-2">
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = now()->year - 5; $i <= now()->year; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="inout" class="table table-striped table-bordered" style="width:100%">
                    @if(request()->routeIs('gudang.acces.accesin'))
                        <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th>Code Accessories</th>
                            <th>Kode/Invoice</th>
                            <th>Nam Accessories</th>
                            <th>Price</th>
                            <th class="text-center" width="10%">Stok</th>
                            <th>Total Harga</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accesin as $key => $data)
                            <tr>
                                <td class="text-center">{{ tanggal($data->date_in) }}</td>
                                <td>{{ $data->accessories->code_acces }}</td>
                                <td>{{ $data->kode_msk }}</td>
                                <td>
                                    <a class="text-dark">{{ $data->accessories->name }}</a>
                                </td>
                                <td>
                                    <a class="text-dark">{{ formatRupiah($data->accessories->price) }},-</a>
                                </td>
                                <td class="text-center">{{ $data->qty }}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>
                                    <button data-bs-toggle="modal"
                                            data-bs-target="#exampleVerticallycenteredModal{{$data->id}}"
                                            class="btn btn-dnd btn-sm bx bx-edit ms-2" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Edit">
                                    </button>
                                    <div class="modal fade" id="exampleVerticallycenteredModal{{$data->id}}" tabindex="-1"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Data</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <form action="{{route('gudang.acces.updatein', $data->id)}}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <label class="col-form-label">Invoice</label>
                                                        <input value="{{$data->kode_msk}}" type="text" name="kode_msk"
                                                               class="form-control" placeholder="Enter Invoice">
                                                        <label class="col-form-label">Tanggal Masuk</label>
                                                        <input value="{{$data->date_in}}" type="text" name="date_in"
                                                               class="form-control datepicker" placeholder="Enter Date In">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">Save<i
                                                                class="bx bx-save"></i></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    @else
                        <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th class="text-center">Tanggal</th>
                            <th>Code Access</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Capital Price</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody id="accesout-data">
                        {{-- Data akan dimuat melalui AJAX --}}
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')
@endpush

@push('js')
    <script>
        $(document).ready(function () {
            function loadData(bulan = '', tahun = '') {
                $.ajax({
                    url: "{{ route('gudang.acces.accesout') }}",
                    type: "GET",
                    data: {bulan: bulan, tahun: tahun},
                    success: function (response) {
                        let tableRows = '';
                        $.each(response, function (accessoryId, data) {
                            tableRows += `
                            <tr>
                                <td colspan="3"><strong>Stok Awal:</strong> ${data.stok_awal}</td>
                                <td colspan="2"><strong>Total Keluar:</strong> ${data.total_keluar}</td>
                                <td colspan="3"><strong>Sisa Stok:</strong> ${data.stok_sisa}</td>
                            </tr>
                        `;
                            $.each(data.data, function (index, acces) {
                                tableRows += `
                                <tr>
                                    <td>${acces.sale.invoice}</td>
                                    <td>${acces.acces_out}</td>
                                    <td>${acces.accessories.code_acces}</td>
                                    <td>${acces.accessories.name}</td>
                                    <td>${acces.accessories.price}</td>
                                    <td>${acces.accessories.capital_price}</td>
                                    <td>${acces.qty}</td>
                                    <td>${acces.total_price}</td>
                                </tr>`;
                            });

                        });
                        $('#accesout-data').html(tableRows);
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memuat data.');
                    }
                });
            }

            // Panggil fungsi loadData pertama kali
            loadData();

            // Event listener untuk filter
            $('#filterForm select').on('change', function () {
                const bulan = $('#bulan').val();
                const tahun = $('#tahun').val();
                loadData(bulan, tahun);
            });
            var table = $('#inout').DataTable({
                lengthChange: false,
                buttons: [
                    {
                        extend: 'pdf',
                        exportOptions: {
                            stripHtml: false,
                        },
                        customize: function (doc) {
                            doc.content = [];

                            doc.pageSize = {
                                width: 842,
                                height: 595
                            };
                            doc.pageOrientation = 'landscape';

                            doc.pageMargins = [20, 20, 20, 20];

                            var thead = $('#inout thead').clone();
                            var headers = [];
                            thead.find('th').each(function () {
                                headers.push({text: $(this).text(), style: 'tableHeader'});
                            });

                            var tableBody = [];
                            tableBody.push(headers);

                            $('#inout tbody tr').each(function () {
                                var row = [];
                                $(this).find('td').each(function () {
                                    var cellText = $(this).text();
                                    if ($(this).find('ul').length > 0) {
                                        cellText = $(this).find('ul').html().replace(/<\/?li>/g, '');
                                        cellText = cellText.split('</li>').filter(item => item).map(item => ({text: item.trim()}));
                                    }
                                    row.push({text: cellText, style: 'tableCell'});
                                });
                                while (row.length < headers.length) {
                                    row.push({text: ''});
                                }
                                tableBody.push(row);
                            });

                            var tfoot = $('#inout tfoot').clone();
                            if (tfoot.length) {
                                var footerRow = [];
                                tfoot.find('th').each(function () {
                                    footerRow.push({text: $(this).text(), style: 'tableCell'});
                                });
                                while (footerRow.length < headers.length) {
                                    footerRow.push({text: ''});
                                }
                                tableBody.push(footerRow);
                            }

                            doc.content.push({
                                table: {
                                    headerRows: 1,
                                    body: tableBody,
                                    widths: Array(headers.length).fill('*'),
                                    style: 'table'
                                },
                                layout: 'lightHorizontalLines'
                            });

                            doc.styles = {
                                table: {
                                    margin: [0, 5, 0, 15]
                                },
                                tableHeader: {
                                    bold: true,
                                    fontSize: 12,
                                    fillColor: '#f2f2f2'
                                },
                                tableCell: {
                                    fontSize: 10
                                }
                            };
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            stripHtml: false,
                        },
                        customize: function (win) {
                            $(win.document.body).find('table').addClass('compact').css('font-size', '10px');
                            var bodyContent = $('#inout tbody').clone();
                            $(win.document.body).find('table').append(bodyContent);
                            var footerContent = $('#inout tfoot').clone();
                            $(win.document.body).find('table').append(footerContent);
                        }
                    }
                ]
            });

            table.buttons().container()
                .appendTo('#inout_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
