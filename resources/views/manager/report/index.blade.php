@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="col">
                <div class="row">
                    <div class="col-sm">
                        <h4 class="mb-0 text-uppercase">Transaction Report</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr/>
    <div class="card table-timbang">
        <div class="card-head">
            <div class="row">
                <form id="filter" method="GET">
                    <div class="row">
                        <div class="col-5 ms-2 mt-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="starDate">
                        </div>
                        <div class="col-6 mt-2">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="endDate">
                        </div>
                    </div>
                    <div class="col-md-1 pt-2 float-end me-5">
                        <button type="button" id="filter-btn" class="btn btn-success btn-sm"><i class="bx bx-filter"></i> Filter</button>
                    </div>
                    <div class="col-md-1 pt-2 float-end ms-5">
                        <button type="button" id="reset-btn" class="btn btn-danger btn-sm"><i class="bx bx-x-circle"></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="filter-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th class="text-center" width="5%">Tanggal</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Accessories</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Total Price</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Total Pay</th>
                        <th class="text-center" width="5%">Tgl Pembayaran</th>
                    </tr>
                    </thead>
                    <tbody id="report-body">
                    <!-- Data will be inserted here via AJAX -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="5" class="text-center">Total Income</th>
                        <th colspan="6" class="text-center" id="total-income">0</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">Profit</th>
                        <th colspan="6" class="text-center" id="profit">0</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">PPN</th>
                        <th colspan="6" class="text-center" id="ppn">0</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">PPH</th>
                        <th colspan="6" class="text-center" id="pph">0</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">Diskon</th>
                        <th colspan="6" class="text-center" id="diskon">0</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-center">Ongkir</th>
                        <th colspan="6" class="text-center" id="ongkir">0</th>
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
        $(document).ready(function () {
            function loadData(startDate = '', endDate = '') {
                $.ajax({
                    url: '{{ route("manager.report.filter") }}',
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function (response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }

                        var reportBody = $('#report-body');
                        reportBody.empty(); // Clear existing data

                        var totalIncome = response.income;
                        $('#total-income').text(formatRupiah(totalIncome));
                        var profit = response.profit;
                        $('#profit').text(formatRupiah(profit));
                        var totalDiskon = response.diskon;
                        $('#diskon').text(formatRupiah(totalDiskon));
                        var totalOngkir = response.ongkir;
                        $('#ongkir').text(formatRupiah(totalOngkir));
                        var totalppn = response.ppn;
                        $('#ppn').text(formatRupiah(totalppn));
                        var totalpph = response.pph;
                        $('#pph').text(formatRupiah(totalpph));

                        response.report.forEach(function (data, index) {
                            // Generate list of item sales as a vertical list
                            var itemSalesList = '<ul>';
                            if (data.itemSales && data.itemSales.length > 0) {
                                data.itemSales.forEach(function(item) {
                                    itemSalesList += `<li>${item}</li>`;
                                });
                            }
                            itemSalesList += '</ul>';

                            // Generate list of accessories as a vertical list
                            var accessoriesList = '<ul>';
                            if (data.accessories && data.accessories.length > 0) {
                                data.accessories.forEach(function(accessory) {
                                    accessoriesList += `<li>${accessory.name} - (${accessory.pivot.qty})</li>`;
                                });
                            }
                            accessoriesList += '</ul>';

                            var debtList = '<ul>';
                            if (data.debt && data.debt.length > 0) {
                                data.debt.forEach(function(debt) {
                                    // Sesuaikan properti yang ingin ditampilkan
                                    var bankName = debt.bank ? debt.bank.name : '';
                                    var description = debt.description ? debt.description : '';
                                    var datePay = debt.date_pay ? debt.date_pay : 'Tanggal tidak tersedia';

                                    debtList += `<li>${datePay} - ${bankName || description}</li>`;
                                });
                            }
                            debtList += '</ul>';

                            var row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatDate(data.created_at)}</td>
                                <td>${data.customer ? data.customer.name : 'N/A'}</td>
                                <td>${itemSalesList}</td>
                                <td>${accessoriesList}</td>
                                <td class="text-center">${data.total_item}</td>
                                <td>${formatRupiah(data.total_price)}</td>
                                <td>${formatRupiah(data.diskon)}</td>
                                <td>${formatRupiah(data.ongkir)}</td>
                                <td>${formatRupiah(data.pay)}</td>
                                <td>${debtList}</td>
                            </tr>
                        `;
                            reportBody.append(row);
                        });
                    },
                    error: function (xhr) {
                        alert('An error occurred while processing the request.');
                    }
                });
            }

            loadData();

            $('#filter-btn').on('click', function () {
                var startDate = $('input[name="start_date"]').val();
                var endDate = $('input[name="end_date"]').val();

                loadData(startDate, endDate);
            });

            $('#reset-btn').click(function () {
                $('#starDate').val('');
                $('#endDate').val('');
                loadData();
            });

            function formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = { day: 'numeric', month: 'short', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options).replace('Des', 'Des'); // Pastikan singkatan sesuai
            }

            var table = $('#filter-table').DataTable({
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

                            var thead = $('#filter-table thead').clone();
                            var headers = [];
                            thead.find('th').each(function() {
                                headers.push({ text: $(this).text(), style: 'tableHeader' });
                            });

                            var tableBody = [];
                            tableBody.push(headers);

                            $('#filter-table tbody tr').each(function() {
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

                            var tfoot = $('#filter-table tfoot').clone();
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
                            var bodyContent = $('#filter-table tbody').clone();
                            $(win.document.body).find('table').append(bodyContent);
                            var footerContent = $('#filter-table tfoot').clone();
                            $(win.document.body).find('table').append(footerContent);
                        }
                    }
                ]
            });

            table.buttons().container()
                .appendTo('#filter-table_wrapper .col-md-6:eq(0)');
        });
    </script>

@endpush
