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
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Total Price</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Total Pay</th>
                    </tr>
                    </thead>
                    <tbody id="report-body">
                    <!-- Data will be inserted here via AJAX -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="5" class="text-center">Total Income</th>
                        <th colspan="3" class="text-center" id="total-income">0</th>
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
                    url: '{{ route("report.filter") }}',
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

                        response.report.forEach(function (data, index) {
                            var row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${formatDate(data.created_at)}</td>
                                    <td>${data.customer.name}</td>
                                    <td class="text-center">${data.total_item}</td>
                                    <td>${formatRupiah(data.total_price)}</td>
                                    <td>${formatRupiah(data.diskon)}</td>
                                    <td>${formatRupiah(data.ongkir)}</td>
                                    <td>${formatRupiah(data.pay)}</td>
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

            // Load all data on page load
            loadData();

            $('#filter-btn').on('click', function () {
                var startDate = $('input[name="start_date"]').val();
                var endDate = $('input[name="end_date"]').val();

                loadData(startDate, endDate);
            });

            $('#reset-btn').click(function () {
                // Clear the date inputs
                $('#starDate').val('');
                $('#endDate').val('');

                // Reload data without filters
                loadData();
            });

            function formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options);
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

                            var thead = $('#filter-table thead').clone();
                            var headers = [];
                            thead.find('th').each(function() {
                                headers.push({ text: $(this).text(), style: 'tableHeader' });
                            });
                            doc.content.push({
                                table: {
                                    headerRows: 1,
                                    body: [headers],
                                    style: 'table'
                                },
                                layout: 'lightHorizontalLines'
                            });

                            $('#filter-table tbody tr').each(function() {
                                var row = [];
                                $(this).find('td').each(function() {
                                    row.push({ text: $(this).text(), style: 'tableCell' });
                                });
                                while (row.length < headers.length) {
                                    row.push({ text: '' });
                                }
                                doc.content[0].table.body.push(row);
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
                                doc.content[0].table.body.push(footerRow);
                            }

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