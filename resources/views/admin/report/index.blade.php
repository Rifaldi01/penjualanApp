@extends('layouts.master')
@section('title', 'LAPORAN TRANSAKSI')
@section('content')
    <div class="">
        <div class="text-danger">
            <span>* No. Invoice manual baru tersedia di Divisi PTP</span><br>
            <span>* Divisi selain PTP masih menggunakan invoice normal</span>
        </div>
    </div>
    <div class="card table-timbang">
        <div class="card-header">
            <div class="row">
                <form id="filter" method="GET">
                    <div class="row">
                        <div class="col-5 ms-2 mt-2">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" id="starDate">
                        </div>
                        <div class="col-6 mt-2">
                            <label class="form-label">Tanggal Berakhir</label>
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
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Invoice Manual</th>
                        <th class="text-center">Invoice</th>
                        <th>Pelanggan</th>
                        <th>Alat</th>
                        <th>Aksesoris</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-center">Total Invoice</th>
                        <th class="text-center">PPN</th>
                        <th class="text-center">PPH</th>
                        <th class="text-center">Diskon</th>
                        <th class="text-center">Ongkir</th>
                        <th class="text-center">Biaya Admin</th>
                        <th class="text-center">Diterima</th>
                        <th class="text-center">Piutang</th>
                        <th class="text-center">Total Bayar</th>
                        <th class="text-center">Fee</th>
                        <th class="text-center">Laba-Rugi</th>
                        <th class="text-center">Tgl Pembayaran</th>
                    </tr>
                    </thead>

                    <tbody id="report-body">
                    <!-- Data will be inserted here via AJAX -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="text-center" colspan="8">Total</th>
                        <th class="text-center" id="ttl_inv"></th>
                        <th class="text-center" id="ttl_ppn"></th>
                        <th class="text-center" id="ttl_pph"></th>
                        <th class="text-center" id="ttl_diskon"></th>
                        <th class="text-center" id="ttl_ongkir"></th>
                        <th class="text-center" id="ttl_biaya_admin"></th>
                        <th class="text-center" id="ttl_diterima"></th>
                        <th class="text-center" id="ttl_piutang"></th>
                        <th class="text-center" id="ttl_bayar"></th>
                        <th class="text-center" id="ttl_fee"></th>
                        <th class="text-center" id="ttl_laba"></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Total Invoice</th>
                        <th colspan="10" class="text-center" id="total-bersih">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Total Bersih</th>
                        <th colspan="10" class="text-center" id="total-income">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Laba-Rugi</th>
                        <th colspan="10" class="text-center" id="profit">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">PPN</th>
                        <th colspan="10" class="text-center" id="ppn">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">PPH</th>
                        <th colspan="10" class="text-center" id="pph">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Fee</th>
                        <th colspan="10" class="text-center" id="fee">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Diskon</th>
                        <th colspan="10" class="text-center" id="diskon">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Ongkir</th>
                        <th colspan="10" class="text-center" id="ongkir">0</th>
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
                        $('#ttl_inv').text(formatRupiah(response.footer.total_invoice));
                        $('#ttl_ppn').text(formatRupiah(response.footer.ppn));
                        $('#ttl_pph').text(formatRupiah(response.footer.pph));
                        $('#ttl_diskon').text(formatRupiah(response.footer.diskon));
                        $('#ttl_ongkir').text(formatRupiah(response.footer.ongkir));
                        $('#ttl_biaya_admin').text(formatRupiah(response.footer.admin));
                        $('#ttl_diterima').text(formatRupiah(response.footer.diterima));
                        $('#ttl_piutang').text(formatRupiah(response.footer.piutang));
                        $('#ttl_bayar').text(formatRupiah(response.footer.total_bayar));
                        $('#ttl_fee').text(formatRupiah(response.footer.fee));
                        $('#ttl_modal').text(formatRupiah(response.footer.modal));
                        $('#ttl_laba').text(formatRupiah(response.footer.laba));

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
                                data.inv_manual ?? '',
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
                                formatRupiah(data.admin_fee),
                                formatRupiah(data.nominal_in ?? 0),
                                formatRupiah(Math.max((data.pay ?? 0) - (data.nominal_in ?? 0), 0)),
                                formatRupiah(data.pay ?? 0),
                                formatRupiah(data.fee ?? 0),
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

            let summaryData = {
                totalprice: 0,
                ppn: 0,
                pph: 0,
                diskon: 0,
                ongkir: 0,
                income: 0,
                admin_fee: 0,
                nominal_in: 0,
                pay: 0,
                fee: 0,
                profit: 0
            };

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
                paginate: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Laporan Transaksi',
                        text: 'Excel',
                        exportOptions: {
                            stripHtml: false
                        },
                        filename: function () {
                            const today = new Date();
                            const yyyy = today.getFullYear();
                            const mm = String(today.getMonth() + 1).padStart(2, '0');
                            const dd = String(today.getDate()).padStart(2, '0');
                            return 'laporan transaksi ' + yyyy + '-' + mm + '-' + dd;
                        },
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var $sheet = $(sheet);
                            var styles = xlsx.xl['styles.xml'];
                            var $styles = $(styles);

                            // jumlah font yang ada
                            var fontCount = parseInt($styles.find('fonts').attr('count'));

// Tambah font merah bold
                            $styles.find('fonts').append(`
<font>
    <b/>
    <sz val="11"/>
    <color rgb="FFFF0000"/>
    <name val="Calibri"/>
</font>
`);

                            $styles.find('fonts').attr('count', fontCount + 1);

// jumlah cellXfs
                            var xfCount = parseInt($styles.find('cellXfs').attr('count'));

                            $styles.find('cellXfs').append(`
<xf xfId="0"
    fontId="${fontCount}"
    fillId="0"
    borderId="0"
    applyFont="1"/>
`);

                            $styles.find('cellXfs').attr('count', xfCount + 1);

                            var redStyle = xfCount;
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
                                const el = document.getElementById(id);
                                return el ? el.innerText : '0';
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
                            var row =
                                `<row r="${rowStart}">
    <c t="inlineStr" r="A${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="B${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="C${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="D${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="E${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="F${rowStart}"><is><t></t></is></c>
    <c t="inlineStr" r="G${rowStart}"><is><t></t></is></c>
    <c s="${redStyle}" t="inlineStr" r="H${rowStart}"><is><t>TOTAL</t></is></c>

    <c s="${redStyle}" t="inlineStr" r="I${rowStart}">
        <is><t>${getFooterText('ttl_inv')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="J${rowStart}">
        <is><t>${getFooterText('ttl_ppn')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="K${rowStart}">
        <is><t>${getFooterText('ttl_pph')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="L${rowStart}">
        <is><t>${getFooterText('ttl_diskon')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="M${rowStart}">
        <is><t>${getFooterText('ttl_ongkir')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="N${rowStart}">
        <is><t>${getFooterText('ttl_biaya_admin')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="O${rowStart}">
        <is><t>${getFooterText('ttl_diterima')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="P${rowStart}">
        <is><t>${getFooterText('ttl_piutang')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="Q${rowStart}">
        <is><t>${getFooterText('ttl_bayar')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="R${rowStart}">
        <is><t>${getFooterText('ttl_fee')}</t></is>
    </c>
    <c s="${redStyle}" t="inlineStr" r="S${rowStart}">
        <is><t>${getFooterText('ttl_laba')}</t></is>
    </c>
</row>`;

                            $sheet.find('sheetData').append(row);
                            rowStart++;

                            var rowStart = $sheet.find('sheetData row').length + 1;
                            addFooterRow('Total Invoice', formatRupiah(summaryData.totalprice), rowStart++);
                            addFooterRow('PPN', formatRupiah(summaryData.ppn), rowStart++);
                            addFooterRow('PPH', formatRupiah(summaryData.pph), rowStart++);
                            addFooterRow('Diskon', formatRupiah(summaryData.diskon), rowStart++);
                            addFooterRow('Ongkir', formatRupiah(summaryData.ongkir), rowStart++);
                            addFooterRow('Total Bersih', formatRupiah(summaryData.income), rowStart++);
                            addFooterRow('Biaya Admin', formatRupiah(summaryData.admin_fee), rowStart++);
                            addFooterRow('Diterima', formatRupiah(summaryData.nominal_in), rowStart++);
                            addFooterRow(
                                'Piutang',
                                formatRupiah(Math.max(summaryData.pay - summaryData.nominal_in, 0)),
                                rowStart++
                            );
                            addFooterRow('Laba-Rugi', formatRupiah(summaryData.profit), rowStart++);
                            addFooterRow('Fee', formatRupiah(summaryData.fee), rowStart++);
                        }

                    }, {
                        extend: 'pdf',
                        text: 'PDF',
                        exportOptions: {
                            page: 'all',
                            columns: ':visible'
                        },
                        filename: function () {
                            const today = new Date();
                            const yyyy = today.getFullYear();
                            const mm = String(today.getMonth() + 1).padStart(2, '0');
                            const dd = String(today.getDate()).padStart(2, '0');
                            return 'laporan transaksi ' + yyyy + '-' + mm + '-' + dd;
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
                                else if (['Total Price', 'Diskon', 'Ongkir', 'Tanggal', 'PPH', 'PPN', 'Diterima', 'Piutang', 'Fee', 'Modal', 'Laba-Rugi', 'Total Bayar'].includes(headerText)) {
                                    widths.push(30); // Perkecil kolom uang
                                } else if (headerText === 'Invoice') {
                                    widths.push(50);
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
                                    fontSize: 4,
                                    fillColor: '#eeeeee',
                                    alignment: 'center'
                                },
                                tableCell: {
                                    fontSize: 3,
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
