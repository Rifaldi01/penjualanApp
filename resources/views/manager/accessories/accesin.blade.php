@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <h4 class="text-uppercase">List Accessories In</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="inout" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th>Code Acces</th>
                        <th>Divisi</th>
                        <th>Kode/Invoice</th>
                        <th>Nam Accessories</th>
                        <th>Price</th>
                        <th class="text-center" width="10%">Stok</th>
                        <th>Total Harga</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accesin as $key => $data)
                        <tr>
                            <td class="text-center">{{ tanggal($data->date_in) }}</td>
                            <td>{{ $data->accessories?->code_acces ?? '-' }}</td>
                            <td>{{ $data->accessories?->divisi?->name ?? '-' }}</td>
                            <td>{{ $data->kode_msk }}</td>
                            <td>
                                <a class="text-dark">{{ $data->accessories?->name ?? '-' }}</a>
                            </td>
                            <td>
                                <a class="text-dark">{{ $data->accessories ? formatRupiah($data->accessories->price) : '0' }}
                                    ,-</a>
                            </td>
                            <td class="text-center">{{ $data->qty }}</td>
                            <td>{{ formatRupiah($data->total_price ?? 0) }}</td>
                        </tr>

                    @endforeach
                    </tbody>
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
