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
                        <div class="col-sm-4 ms-5 mt-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="starDate">
                        </div>
                        <div class="col-sm-4 mt-2">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="endDate">
                        </div>
                        <div class="col-sm-3 mt-2">
                            <label class="form-label">Divisi</label>
                            <select name="divisi_id" id="single-select-optgroup-field"
                                    data-placeholder="--Semua Divisi--" class="form-control accessory-select">
                                <option value=""></option>
                                @foreach($divisi as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 pt-2 float-end me-5">
                        <button type="button" id="filter-btn" class="btn btn-success btn-sm"><i
                                class="bx bx-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-1 pt-2 float-end ms-5">
                        <button type="button" id="reset-btn" class="btn btn-danger btn-sm"><i
                                class="bx bx-x-circle"></i> Reset
                        </button>
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
                        <th class="text-center" width="5%"> Invoice</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Accessories</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Total Invoice</th>
                        <th class="text-center" width="5%">PPN</th>
                        <th class="text-center" width="5%">PPH</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Diterima</th>
                        <th class="text-center" width="5%">Piutang</th>
                        <th class="text-center" width="5%">Total Bayar</th>
                        <th class="text-center" width="5%">Fee</th>
                        <th class="text-center" width="5%">Modal</th>
                        <th class="text-center" width="5%">Laba Untung Rugi</th>
                        <th class="text-center" width="5%">Tgl Pembayaran</th>
                    </tr>
                    </thead>
                    <tbody id="report-body">
                    <!-- Data will be inserted here via AJAX -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total Invoice</th>
                        <th colspan="6" class="text-center" id="total-bersih">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">Total Bersih</th>
                        <th colspan="6" class="text-center" id="total-income">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">Laba-Rugi</th>
                        <th colspan="6" class="text-center" id="profit">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">PPN</th>
                        <th colspan="6" class="text-center" id="ppn">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">PPH</th>
                        <th colspan="6" class="text-center" id="pph">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">Fee</th>
                        <th colspan="6" class="text-center" id="fee">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">Diskon</th>
                        <th colspan="6" class="text-center" id="diskon">0</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-center">Ongkir</th>
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
            function loadData(startDate = '', endDate = '', divisiId ='') {
                $.ajax({
                    url: '{{ route("manager.report.filter") }}',
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        divisi_id: divisiId
                    },
                    success: function (response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }

                        table.clear().draw(); // Hapus semua data di DataTables

                        var totalIncome = response.income;
                        $('#total-income').text(formatRupiah(totalIncome));
                        $('#profit').text(formatRupiah(response.profit));
                        $('#diskon').text(formatRupiah(response.diskon));
                        $('#ongkir').text(formatRupiah(response.ongkir));
                        $('#ppn').text(formatRupiah(response.ppn));
                        $('#pph').text(formatRupiah(response.pph));
                        $('#fee').text(formatRupiah(response.fee));
                        $('#total-bersih').text(formatRupiah(response.totalprice));

                        let totalCapital = response.totalCapital; // <- pastikan ini tidak undefined
                        response.report.forEach(function (data, index) {
                            var itemSalesList = '<ul>';
                            if (data.itemSales && data.itemSales.length > 0) {
                                data.itemSales.forEach(function (item) {
                                    itemSalesList += `<li>${item}</li>`;
                                });
                            }
                            itemSalesList += '</ul>';

                            var accessoriesList = '<ul>';
                            if (data.accessories && data.accessories.length > 0) {
                                data.accessories.forEach(function (accessory) {
                                    accessoriesList += `<li>${accessory.name} - (${accessory.pivot.qty})</li>`;
                                });
                            }
                            accessoriesList += '</ul>';

                            var debtList = '<ul>';
                            if (data.debt && data.debt.length > 0) {
                                data.debt.forEach(function (debt) {
                                    var bankName = debt.bank ? debt.bank.name : '';
                                    var description = debt.description ? debt.description : '';
                                    var datePay = debt.date_pay ? debt.date_pay : 'Tanggal tidak tersedia';
                                    debtList += `<li>${datePay} - ${bankName || description || 'Tunai'}</li>`;
                                });
                            }
                            debtList += '</ul>';

                            // Tambahkan ke DataTable, bukan ke DOM
                            table.row.add([
                                index + 1,
                                formatDate(data.created_at ?? ''),
                                data.invoice ?? 'N/A',
                                data.customer?.name ?? 'N/A',
                                itemSalesList ?? 'N/A',
                                accessoriesList ?? 'N/A',
                                data.total_item ?? 0,
                                formatRupiah(data.total_price ?? 0),
                                formatRupiah(data.ppn ?? 0),
                                formatRupiah(data.pph ?? 0),
                                formatRupiah(data.diskon ?? 0),
                                formatRupiah(data.ongkir ?? 0),
                                formatRupiah(data.nominal_in ?? 0),
                                formatRupiah(Math.max((data.pay ?? 0) - (data.nominal_in ?? 0), 0)),
                                formatRupiah(data.pay ?? 0),
                                formatRupiah(data.fee ?? 0),
                                formatRupiah(totalCapital?.[data.id]),
                                formatRupiah(Math.max((data.pay ?? 0) - (data.fee ?? 0) - (totalCapital?.[data.id]))),
                                debtList ?? 'N/A'
                            ]).draw(false);

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
                var divisiId = $('select[name="divisi_id"]').val(); // Ambil nilai dari dropdown

                loadData(startDate, endDate, divisiId);
            });

            $('#reset-btn').click(function () {
                $('#starDate').val('');
                $('#endDate').val('');
                $('#single-select-optgroup-field').val('').trigger('change');
                loadData(); // akan kirim nilai kosong
            });
            function formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = {day: 'numeric', month: 'short', year: 'numeric'};
                return date.toLocaleDateString('id-ID', options).replace('Des', 'Des'); // Pastikan singkatan sesuai
            }

            var table = $('#filter-table').DataTable({
                lengthChange: false,
                paginate: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Laporan Transaksi',
                        text: 'Excel',
                        exportOptions: {
                            stripHtml: false
                        },
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var $sheet = $(sheet);

                            // Hapus tag HTML <ul> dan <li> dari setiap cell
                            $('row c is t', sheet).each(function () {
                                var cell = $(this);
                                var text = cell.text();

                                // Hapus tag HTML <ul> dan <li>
                                text = text
                                    .replace(/<\/?ul>/g, '')     // hapus <ul> dan </ul>
                                    .replace(/<\/?li>/g, '')     // hapus <li> dan </li>
                                    .replace(/\n/g, '')          // hapus newline jika ada
                                    .trim();                     // hapus spasi di awal/akhir

                                cell.text(text);
                            });

                            // Tambahkan footer income manual
                            function getFooterText(id) {
                                return document.getElementById(id).innerText || '0';
                            }

                            function addFooterRow(label, value, rowNumber) {
                                var row =
                                    `<row r="${rowNumber}">
                                        <c t="inlineStr" r="A${rowNumber}">
                                            <is><t>${label}</t></is>
                                        </c>
                                        <c t="inlineStr" r="B${rowNumber}">
                                            <is><t>${value}</t></is>
                                        </c>
                                    </row>`;
                                $sheet.find('sheetData').append(row);
                            }

                            var rowStart = $sheet.find('sheetData row').length + 1;
                            addFooterRow('Total Invoice', getFooterText('total-bersih'), rowStart++);
                            addFooterRow('Total Bersih', getFooterText('total-income'), rowStart++);
                            addFooterRow('Laba-Rugi', getFooterText('profit'), rowStart++);
                            addFooterRow('PPN', getFooterText('ppn'), rowStart++);
                            addFooterRow('PPH', getFooterText('pph'), rowStart++);
                            addFooterRow('Fee', getFooterText('fee'), rowStart++);
                            addFooterRow('Diskon', getFooterText('diskon'), rowStart++);
                            addFooterRow('Ongkir', getFooterText('ongkir'), rowStart++);
                        }

                    }, {
                        extend: 'pdf',
                        text: 'PDF',
                        exportOptions: {
                            page: 'all',
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            doc.pageSize = 'A4';
                            doc.pageOrientation = 'landscape';
                            doc.pageMargins = [20, 20, 20, 20];

                            let headers = [];
                            let widths = [];
                            $('#filter-table thead th').each(function (index) {
                                let headerText = $(this).text().trim();
                                headers.push({text: headerText, style: 'tableHeader'});

                                if (index === 0) widths.push(15); // No
                                else if (['Total Price', 'Diskon', 'Ongkir', 'Tanggal'].includes(headerText)) {
                                    widths.push(50); // Perkecil kolom uang
                                } else if (headerText === 'Invoice') {
                                    widths.push(90);
                                } else if (headerText === 'Total Item') {
                                    widths.push(20);
                                } else {
                                    widths.push('*'); // Kolom lainnya fleksibel
                                }
                            });


                            let tableBody = [];
                            tableBody.push(headers);

                            $('#filter-table tbody tr').each(function () {
                                let row = [];

                                $(this).find('td').each(function () {
                                    let htmlContent = $(this).html();

                                    // Hapus tag <ul>, <li>, dan spasi kosong berlebihan
                                    let cleanedHtml = htmlContent
                                        .replace(/<\/?(ul|li)>/gi, '')  // hapus tag
                                        .replace(/\s+/g, ' ')          // hapus spasi berlebih
                                        .trim();                       // hapus spasi depan belakang

                                    // Ambil teks bersih
                                    let cleanText = $('<div>').html(cleanedHtml).text();

                                    row.push({text: cleanText, style: 'tableCell'});
                                });

                                while (row.length < headers.length) {
                                    row.push({text: '', style: 'tableCell'});
                                }

                                tableBody.push(row);
                            });

                            // Footer (jika ada)
                            $('#filter-table tfoot tr').each(function () {
                                let row = [];
                                $(this).find('th, td').each(function () {
                                    let text = $(this).text().trim();
                                    row.push({text: text, style: 'tableCell'});
                                });
                                while (row.length < headers.length) {
                                    row.push({text: '', style: 'tableCell'});
                                }
                                tableBody.push(row);
                            });

                            doc.content = [{
                                table: {
                                    headerRows: 1,
                                    widths: widths,
                                    body: tableBody
                                },
                                layout: {
                                    hLineWidth: function () { return 0.5; },
                                    vLineWidth: function () { return 0.5; },
                                    hLineColor: function () { return '#aaa'; },
                                    vLineColor: function () { return '#aaa'; },
                                    paddingLeft: function () { return 4; },
                                    paddingRight: function () { return 4; },
                                    paddingTop: function () { return 3; },
                                    paddingBottom: function () { return 3; }
                                }
                            }];

                            doc.styles = {
                                tableHeader: {
                                    bold: true,
                                    fontSize: 6,
                                    fillColor: '#eeeeee',
                                    alignment: 'center'
                                },
                                tableCell: {
                                    fontSize: 5,
                                    alignment: 'left'
                                }
                            };
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: {
                            stripHtml: false,
                            columns: ':visible'
                        },
                        customize: function (win) {
                            // Set landscape orientation
                            const css = '@page { size: landscape; margin: 10mm; }';
                            const head = win.document.head || win.document.getElementsByTagName('head')[0];
                            const style = win.document.createElement('style');
                            style.type = 'text/css';
                            style.media = 'print';
                            style.appendChild(win.document.createTextNode(css));
                            head.appendChild(style);

                            // Atur ukuran font dan style
                            $(win.document.body).css('font-size', '10px');
                            const $table = $(win.document.body).find('table');

                            $table
                                .addClass('compact')
                                .css({
                                    'font-size': '10px',
                                    'border-collapse': 'collapse',
                                    'width': '100%'
                                });

                            // Hapus tbody dan tfoot default
                            $table.find('tbody').remove();
                            $table.find('tfoot').remove();

                            // Tambahkan tbody dari halaman utama
                            const tbody = $('#filter-table tbody').clone();
                            $table.append(tbody);

                            // Tambahkan footer (tfoot) sebagai div terpisah di akhir body, bukan dalam table
                            const footerHtml = $('<div>')
                                .css({
                                    'margin-top': '20px',
                                    'font-size': '10px'
                                })
                                .append($('#filter-table tfoot').clone());

                            $(win.document.body).append(footerHtml);
                        }
                    }

                ]
            });

            table.buttons().container()
                .appendTo('#filter-table_wrapper .col-md-6:eq(0)');
        });
    </script>

@endpush
