@extends('layouts.master')
@section('title', 'LAPORAN TRANSAKSI')
@section('content')
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
                        <th class="text-center" width="5%">Biaya Admin</th>
                        <th class="text-center" width="5%">Diterima</th>
                        <th class="text-center" width="5%">Piutang</th>
                        <th class="text-center" width="5%">Total Bayar</th>
                        <th class="text-center" width="5%">Fee</th>
                        <th class="text-center" width="5%">Laba Untung Rugi</th>
                        <th class="text-center" width="5%">Tgl Pembayaran</th>
                    </tr>
                    </thead>
                    <tbody id="report-body">
                    <!-- Data will be inserted here via AJAX -->
                    </tbody>
                    <tfoot>
                    <tr>

                        <th class="text-center" colspan="7">Total</th>
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

                        <th colspan="10" class="text-center">Biaya Admin</th>
                        <th colspan="10" class="text-center" id="admin">0</th>
                    </tr>
                    <tr>

                        <th colspan="10" class="text-center">Diskon</th>
                        <th colspan="10" class="text-center" id="diskon">0</th>
                    </tr>
                    <tr>

                        <th colspan="10" class="text-center">Ongkir</th>
                        <th colspan="10" class="text-center" id="ongkir">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Diterima</th>
                        <th colspan="10" class="text-center" id="diterima">0</th>
                    </tr>
                    <tr>
                        <th colspan="10" class="text-center">Piutang</th>
                        <th colspan="10" class="text-center" id="piutang">0</th>
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
            function loadData(startDate = '', endDate = '', divisiId = '') {
                $.ajax({
                    url: '{{ route("report.filter") }}',
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
                        $('#admin').text(formatRupiah(response.admin_fee));
                        $('#fee').text(formatRupiah(response.fee));
                        $('#total-bersih').text(formatRupiah(response.totalprice));
                        $('#piutang').text(formatRupiah(response.footer.piutang));
                        $('#diterima').text(formatRupiah(response.diterima));
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
                        $('#ttl_laba').text(formatRupiah(response.footer.laba));

                        let totalCapital = response.totalCapital;

                        response.report.forEach(function (data, index) {

                            // ================= ITEM SALES =================
                            var itemSalesList = '<ul>';

                            if (data.itemSales && data.itemSales.length > 0) {

                                data.itemSales.forEach(function (item) {
                                    itemSalesList += `
                                    <li>
                                            ${item}
                                    </li>
                                `;
                                });
                            }

                            itemSalesList += '</ul>';
                            // ================= ACCESSORIES =================
                            var accessoriesList = '<ul>';

                            if (data.accessories_sales && data.accessories_sales.length > 0) {

                                data.accessories_sales.forEach(function (detail) {

                                    let qtyTersisa =
                                        parseInt(detail.qty) - parseInt(detail.return_qty ?? 0);
                                    let price = parseInt(detail.price_sale);

                                    accessoriesList += `
                                    <li>
                                            ${detail.accessories.name}
                                            <br> Qty : ${qtyTersisa}
                                            <br> Harga : Rp ${new Intl.NumberFormat('id-ID').format(price)}
                                    </li>
                                `;
                                });

                            } else {

                                accessoriesList += '<li>-</li>';

                            }

                            accessoriesList += '</ul>';


                            // ================= DEBT =================
                            var debtList = '<ul>';

                            if (data.debt && data.debt.length > 0) {
                                data.debt.forEach(function (debt) {
                                    var bankName = debt.bank?.name || '';
                                    var description = debt.description || '';
                                    var payDebt = debt.pay_debts || 0;
                                    var datePay = debt.date_pay || null;
                                    var penerima = debt.penerima || '-';

                                    debtList += `
                                    <li>
                                        ${datePay ? formatDate(datePay) : 'Tanggal tidak tersedia'}<br>
                                        <strong>Metode:</strong> ${bankName || description || 'Tunai'}<br>
                                        <strong>Uang Masuk:</strong> Rp ${new Intl.NumberFormat('id-ID').format(payDebt)}<br>
                                        <strong>Penerima:</strong> ${penerima}
                                    </li>
                                `;
                                });
                            } else {
                                debtList += `<li>-</li>`;
                            }

                            debtList += '</ul>';


                            // ================= DATATABLE =================
                            table.row.add([
                                index + 1,
                                formatDate(data.created_at ?? ''),
                                data.invoice ?? 'N/A',
                                data.customer?.name ?? 'N/A',
                                itemSalesList,
                                accessoriesList,
                                data.total_item ?? 0,
                                formatRupiah(data.total_price ?? 0),
                                formatRupiah(data.ppn ?? 0),
                                formatRupiah(data.pph ?? 0),
                                formatRupiah(data.diskon ?? 0),
                                formatRupiah(data.ongkir ?? 0),
                                formatRupiah(data.admin_fee ?? 0),
                                formatRupiah(data.nominal_in ?? 0),
                                formatRupiah(Math.max((data.pay ?? 0) - (data.nominal_in ?? 0), 0)),
                                formatRupiah(data.pay ?? 0),
                                formatRupiah(data.fee ?? 0),
                                formatRupiah(
                                    (parseFloat(data.pay ?? 0))
                                    - (parseFloat(totalCapital?.[data.id] ?? 0))
                                ),
                                debtList
                            ]).draw(false);

                        });
                    },
                    error: function (xhr) {
                        alert('An error occurred while processing the request.');
                    }
                });
            }

            // loadData();

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
            function cleanExcelHtml(value) {

                if (
                    value === null ||
                    value === undefined
                ) {
                    return '';
                }

                let text = String(value);


                /*
                 * <br> menjadi Enter
                 */
                text = text.replace(
                    /<br\s*\/?>/gi,
                    '\n'
                );


                /*
                 * </li> menjadi Enter
                 */
                text = text.replace(
                    /<\/li>/gi,
                    '\n'
                );


                /*
                 * Hapus semua tag HTML
                 */
                text = text.replace(
                    /<[^>]*>/g,
                    ''
                );


                /*
                 * Decode HTML entity
                 */
                let textarea =
                    document.createElement('textarea');

                textarea.innerHTML =
                    text;

                text =
                    textarea.value;


                /*
                 * Normalisasi Enter
                 */
                text = text
                    .replace(/\r\n/g, '\n')
                    .replace(/\r/g, '\n');


                /*
                 * Bersihkan spasi tanpa menghilangkan Enter
                 */
                text = text
                    .split('\n')
                    .map(function (line) {

                        return line
                            .replace(/[ \t]+/g, ' ')
                            .trim();

                    })
                    .join('\n');


                /*
                 * Maksimal 2 Enter berturut-turut
                 */
                text = text.replace(
                    /\n{3,}/g,
                    '\n\n'
                );


                return text.trim();
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


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 1. TAMBAH STYLE WRAP TEXT
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            var cellXfs = $styles.find('cellXfs');
                            var xfCount = parseInt(cellXfs.attr('count'));

                            cellXfs.append(`
            <xf xfId="0"
                applyAlignment="1">
                <alignment
                    vertical="top"
                    wrapText="1"/>
            </xf>
        `);

                            cellXfs.attr('count', xfCount + 1);

                            var wrapStyle = xfCount;


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 2. TAMBAH FONT MERAH + BOLD
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            var fonts = $styles.find('fonts');

                            var fontCount = parseInt(fonts.attr('count'));

                            fonts.append(`
            <font>
                <b/>
                <sz val="11"/>
                <color rgb="FFFF0000"/>
                <name val="Calibri"/>
                <family val="2"/>
            </font>
        `);

                            fonts.attr('count', fontCount + 1);


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 3. STYLE MERAH + WRAP TEXT
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            var redXfCount = parseInt(cellXfs.attr('count'));

                            cellXfs.append(`
            <xf
                xfId="0"
                fontId="${fontCount}"
                applyFont="1"
                applyAlignment="1">

                <alignment
                    vertical="top"
                    wrapText="1"/>
            </xf>
        `);

                            cellXfs.attr('count', redXfCount + 1);

                            var redStyle = redXfCount;


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 4. BERSIHKAN HTML
                            | ================================================================
                            |
                            | <br>        -> ENTER
                            | <strong>    -> hilangkan tag
                            | <b>         -> hilangkan tag
                            | <ul>        -> hilangkan
                            | <li>        -> ENTER
                            | <a>         -> hilangkan tag
                            |
                            |--------------------------------------------------------------------------
                            */

                            $('row c', sheet).each(function () {

                                var cell = $(this);

                                var textNode = cell.find('is t');

                                if (!textNode.length) {
                                    return;
                                }

                                var text = textNode.text();


                                /*
                                |--------------------------------------------------------------------------
                                | NORMALISASI HTML BREAK
                                |--------------------------------------------------------------------------
                                */

                                text = text
                                    .replace(/<br\s*\/?>/gi, '\n')
                                    .replace(/<\/li>\s*<li>/gi, '\n')
                                    .replace(/<li[^>]*>/gi, '')
                                    .replace(/<\/li>/gi, '')
                                    .replace(/<\/?ul[^>]*>/gi, '')
                                    .replace(/<\/?ol[^>]*>/gi, '')
                                    .replace(/<strong[^>]*>/gi, '')
                                    .replace(/<\/strong>/gi, '')
                                    .replace(/<b[^>]*>/gi, '')
                                    .replace(/<\/b>/gi, '')
                                    .replace(/<span[^>]*>/gi, '')
                                    .replace(/<\/span>/gi, '')
                                    .replace(/<p[^>]*>/gi, '')
                                    .replace(/<\/p>/gi, '\n')
                                    .replace(/<a[^>]*>/gi, '')
                                    .replace(/<\/a>/gi, '')
                                    .replace(/&nbsp;/gi, ' ');


                                /*
                                |--------------------------------------------------------------------------
                                | HAPUS TAG HTML LAIN
                                |--------------------------------------------------------------------------
                                */

                                text = text.replace(/<[^>]+>/g, '');


                                /*
                                |--------------------------------------------------------------------------
                                | NORMALISASI ENTER
                                |--------------------------------------------------------------------------
                                |
                                | Tujuannya:
                                |
                                | GPS Hitam
                                | Qty : 1
                                | Harga : Rp 3.000
                                |
                                | BUKAN:
                                |
                                | GPS Hitam
                                |
                                | Qty : 1
                                |
                                | Harga : Rp 3.000
                                |
                                |--------------------------------------------------------------------------
                                */

                                text = text
                                    .replace(/\r\n/g, '\n')
                                    .replace(/\r/g, '\n')
                                    .replace(/[ \t]+\n/g, '\n')
                                    .replace(/\n[ \t]+/g, '\n')
                                    .replace(/\n{2,}/g, '\n')
                                    .trim();


                                /*
                                |--------------------------------------------------------------------------
                                | GANTI TEXT CELL
                                |--------------------------------------------------------------------------
                                */

                                textNode.text(text);


                                /*
                                |--------------------------------------------------------------------------
                                | APPLY WRAP TEXT
                                |--------------------------------------------------------------------------
                                */

                                cell.attr('s', wrapStyle);


                                /*
                                |--------------------------------------------------------------------------
                                | SET TYPE INLINE STRING
                                |--------------------------------------------------------------------------
                                */

                                cell.attr('t', 'inlineStr');

                            });


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 5. FUNGSI FOOTER
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            function getFooterText(id) {

                                var element = document.getElementById(id);

                                if (!element) {
                                    return '0';
                                }

                                var value = element.innerText || element.textContent || '0';

                                /*
                                 * Bersihkan HTML jika masih ada
                                 */

                                value = value
                                    .replace(/<br\s*\/?>/gi, '\n')
                                    .replace(/<[^>]+>/g, '')
                                    .replace(/\n{2,}/g, '\n')
                                    .trim();

                                return value || '0';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 6. ESCAPE XML
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            function escapeXml(value) {

                                return String(value)
                                    .replace(/&/g, '&amp;')
                                    .replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/"/g, '&quot;')
                                    .replace(/'/g, '&apos;');
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 7. FOOTER TOTAL
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            var lastRow = $sheet.find('sheetData row').last();

                            var rowStart =
                                parseInt(lastRow.attr('r') || 1) + 1;


                            /*
                            |--------------------------------------------------------------------------
                            | TOTAL PER KOLOM
                            |--------------------------------------------------------------------------
                            */

                            var totalRow = `
            <row r="${rowStart}">

                <c s="${redStyle}" t="inlineStr" r="A${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="B${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="C${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="D${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="E${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="F${rowStart}">
                    <is><t></t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="G${rowStart}">
                    <is><t>TOTAL</t></is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="H${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_inv'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="I${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_ppn'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="J${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_pph'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="K${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_diskon'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="L${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_ongkir'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="M${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_biaya_admin'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="N${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_diterima'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="O${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_piutang'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="P${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_bayar'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="Q${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_fee'))}</t>
                    </is>
                </c>

                <c s="${redStyle}" t="inlineStr" r="R${rowStart}">
                    <is>
                        <t>${escapeXml(getFooterText('ttl_laba'))}</t>
                    </is>
                </c>

            </row>
        `;

                            $sheet.find('sheetData').append(totalRow);


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 8. FOOTER RINGKASAN
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            rowStart++;


                            function addFooterRow(label, value, rowNumber) {

                                label = escapeXml(label);
                                value = escapeXml(value);

                                var row = `
                <row r="${rowNumber}">

                    <c
                        s="${redStyle}"
                        t="inlineStr"
                        r="A${rowNumber}">

                        <is>
                            <t>${label}</t>
                        </is>

                    </c>

                    <c
                        s="${redStyle}"
                        t="inlineStr"
                        r="B${rowNumber}">

                        <is>
                            <t>${value}</t>
                        </is>

                    </c>

                </row>
            `;

                                $sheet.find('sheetData').append(row);
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TOTAL INVOICE
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Total Invoice',
                                getFooterText('total-bersih'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | TOTAL BERSIH
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Total Bersih',
                                getFooterText('total-income'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | LABA RUGI
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Laba-Rugi',
                                getFooterText('profit'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PPN
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'PPN',
                                getFooterText('ppn'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PPH
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'PPH',
                                getFooterText('pph'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | BIAYA ADMIN
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Biaya Admin',
                                getFooterText('admin'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | FEE
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Fee',
                                getFooterText('fee'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | DISKON
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Diskon',
                                getFooterText('diskon'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | ONGKIR
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Ongkir',
                                getFooterText('ongkir'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | DITERIMA
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Diterima',
                                getFooterText('diterima'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PIUTANG
                            |--------------------------------------------------------------------------
                            */

                            addFooterRow(
                                'Piutang',
                                getFooterText('piutang'),
                                rowStart++
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 9. SET SEMUA CELL ACCESSORIES / TANGGAL PEMBAYARAN WRAP
                            | ================================================================
                            |--------------------------------------------------------------------------
                            */

                            $('row c', sheet).each(function () {

                                var cell = $(this);

                                /*
                                 * Kalau cell sudah memiliki style,
                                 * jangan menghilangkan style merah footer.
                                 */

                                var currentStyle = cell.attr('s');

                                if (
                                    currentStyle === undefined ||
                                    currentStyle === null ||
                                    currentStyle === ''
                                ) {

                                    cell.attr('s', wrapStyle);

                                }

                            });


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 10. SETTING ROW HEIGHT
                            | ================================================================
                            |
                            | Tidak dibuat terlalu tinggi.
                            | Excel akan tetap menampilkan satu ENTER per baris.
                            |
                            |--------------------------------------------------------------------------
                            */

                            $('row', sheet).each(function () {

                                var row = $(this);

                                /*
                                 * Jangan paksa height besar.
                                 * Excel akan menyesuaikan berdasarkan wrap text.
                                 */

                                row.attr('customHeight', '0');

                            });


                            /*
                            |--------------------------------------------------------------------------
                            | ================================================================
                            | 11. SET WIDTH KOLOM
                            | ================================================================
                            */

                            var cols = $sheet.find('cols');

                            if (cols.length) {

                                cols.find('col').each(function () {

                                    var col = $(this);

                                    var min = parseInt(
                                        col.attr('min') || 0
                                    );

                                    var max = parseInt(
                                        col.attr('max') || 0
                                    );


                                    /*
                                     * Kolom Accessories
                                     *
                                     * Sesuaikan nomor kolom jika Accessories
                                     * berada pada kolom berbeda.
                                     */

                                    if (min === 5 || max === 5) {

                                        col.attr('width', '35');

                                    }

                                });

                            }

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
                                    hLineWidth: function () {
                                        return 0.5;
                                    },
                                    vLineWidth: function () {
                                        return 0.5;
                                    },
                                    hLineColor: function () {
                                        return '#aaa';
                                    },
                                    vLineColor: function () {
                                        return '#aaa';
                                    },
                                    paddingLeft: function () {
                                        return 4;
                                    },
                                    paddingRight: function () {
                                        return 4;
                                    },
                                    paddingTop: function () {
                                        return 3;
                                    },
                                    paddingBottom: function () {
                                        return 3;
                                    }
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
            loadData();
        });
    </script>
    <script>

        document.addEventListener("DOMContentLoaded", function () {

            let now = new Date();

            let firstDay =
                now.getFullYear() + "-" +
                String(now.getMonth() + 1).padStart(2, '0') +
                "-01";

            let lastDay =
                now.getFullYear() + "-" +
                String(now.getMonth() + 1).padStart(2, '0') +
                "-" +
                new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();

            $('#starDate').val(firstDay);
            $('#endDate').val(lastDay);

        });

    </script>
@endpush
