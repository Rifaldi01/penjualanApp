@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">

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
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="inout" class="table table-striped table-bordered" style="width:100%">

                        <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th class="text-center">Tanggal</th>
                            <th>Code Access</th>
                            <th>Divsi</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody id="accesout-data">
                        {{-- Data akan dimuat melalui AJAX --}}
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
                        customize: function(doc) {
                            doc.content = [];

                            doc.pageSize = {
                                width: 842,
                                height: 595
                            };
                            doc.pageOrientation = 'landscape';

                            doc.pageMargins = [20, 20, 20, 20];

                            var thead = $('#inout thead').clone();
                            var headers = [];
                            thead.find('th').each(function() {
                                headers.push({ text: $(this).text(), style: 'tableHeader' });
                            });

                            var tableBody = [];
                            tableBody.push(headers);

                            $('#inout tbody tr').each(function() {
                                var row = [];
                                $(this).find('td').each(function() {
                                    var cellText = $(this).text();
                                    if ($(this).find('ul').length > 0) {
                                        cellText = $(this).find('ul').html().replace(/<\/?li>/g, '');
                                        cellText = cellText.split('</li>').filter(item => item).map(item => ({ text: item.trim() }));
                                    }
                                    row.push({ text: cellText, style: 'tableCell' });
                                });
                                while (row.length < headers.length) {
                                    row.push({ text: '' });
                                }
                                tableBody.push(row);
                            });

                            var tfoot = $('#inout tfoot').clone();
                            if (tfoot.length) {
                                var footerRow = [];
                                tfoot.find('th').each(function() {
                                    footerRow.push({ text: $(this).text(), style: 'tableCell' });
                                });
                                while (footerRow.length < headers.length) {
                                    footerRow.push({ text: '' });
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
                        customize: function(win) {
                            $(win.document.body).find('table').addClass('compact').css('font-size', '10px');
                            var bodyContent = $('#inout tbody').clone();
                            $(win.document.body).find('table').append(bodyContent);
                            var footerContent = $('#inout tfoot').clone();
                            $(win.document.body).find('table').append(footerContent);
                        }
                    }
                ]
            });

            function loadData(bulan = '', tahun = '') {
                $.ajax({
                    url: "{{ route('manager.acces.accesout') }}",
                    type: "GET",
                    data: { bulan: bulan, tahun: tahun },
                    success: function (response) {
                        let tableRows = '';
                        $.each(response, function (accessoryId, data) {
                            tableRows += `
                            <tr>
                                <td colspan="3"><strong>Stok Awal:</strong> ${data.stok_awal}</td>
                                <td colspan="3"><strong>Total Keluar:</strong> ${data.total_keluar}</td>
                                <td colspan="2"><strong>Sisa Stok:</strong> ${data.stok_sisa}</td>
                            </tr>
                        `;
                            $.each(data.data, function (index, acces) {
                                tableRows += `
                                <tr>
                                    <td>${acces.sale.invoice}</td>
                                    <td>${acces.acces_out}</td>
                                    <td>${acces.accessories.code_acces}</td>
                                    <td>${acces.accessories.divisi.name}</td>
                                    <td>${acces.accessories.name}</td>
                                    <td>${acces.accessories.price}</td>
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


            table.buttons().container()
                .appendTo('#inout_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
