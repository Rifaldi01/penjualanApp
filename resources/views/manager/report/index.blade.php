@extends('layouts.master')

@section('title', 'LAPORAN TRANSAKSI MANAGER')

@section('content')

    <div class="">
        <div class="text-danger">
            <span>* Laporan menampilkan transaksi dari seluruh divisi</span><br>
            <span>* Gunakan filter divisi untuk melihat transaksi divisi tertentu</span>
        </div>
    </div>


    <div class="card table-timbang">

        {{-- =========================================================
             FILTER
        ========================================================== --}}

        <div class="card-header">

            <div class="row">

                <form id="filter" method="GET">

                    <div class="row">

                        {{-- TANGGAL MULAI --}}
                        <div class="col-md-3 ms-2 mt-2">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                name="start_date"
                                id="starDate"
                            >

                        </div>


                        {{-- TANGGAL BERAKHIR --}}
                        <div class="col-md-3 mt-2">

                            <label class="form-label">
                                Tanggal Berakhir
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                name="end_date"
                                id="endDate"
                            >

                        </div>


                        {{-- DIVISI --}}
                        <div class="col-md-4 mt-2">

                            <label class="form-label">
                                Divisi
                            </label>

                            <select
                                name="divisi_id"
                                id="divisi_id"
                                class="form-select"
                            >

                                <option value="all">
                                    Semua Divisi
                                </option>

                                @foreach($divisis as $divisi)

                                    <option
                                        value="{{ $divisi->id }}"
                                    >
                                        {{ $divisi->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- BUTTON FILTER --}}
                    <div class="col-md-1 pt-2 float-end me-5">

                        <button
                            type="button"
                            id="filter-btn"
                            class="btn btn-success btn-sm"
                        >

                            <i class="bx bx-filter"></i>

                            Filter

                        </button>

                    </div>


                    {{-- BUTTON RESET --}}
                    <div class="col-md-1 pt-2 float-end ms-5">

                        <button
                            type="button"
                            id="reset-btn"
                            class="btn btn-danger btn-sm"
                        >

                            <i class="bx bx-x-circle"></i>

                            Reset

                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- =========================================================
             TABLE
        ========================================================== --}}

        <div class="card-body">

            <div class="table-responsive">

                <table
                    id="filter-table"
                    class="table table-striped table-bordered"
                    style="width:100%"
                >

                    <thead>

                    <tr>

                        <th width="4%">
                            No
                        </th>

                        <th class="text-center">
                            Tanggal
                        </th>

                        <th class="text-center">
                            Divisi
                        </th>

                        <th class="text-center">
                            Invoice Manual
                        </th>

                        <th class="text-center">
                            Invoice
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Alat
                        </th>

                        <th>
                            Aksesoris
                        </th>

                        <th class="text-center">
                            Total Item
                        </th>

                        <th class="text-center">
                            Total Invoice
                        </th>

                        <th class="text-center">
                            PPN
                        </th>

                        <th class="text-center">
                            PPH
                        </th>

                        <th class="text-center">
                            Diskon
                        </th>

                        <th class="text-center">
                            Ongkir
                        </th>

                        <th class="text-center">
                            Biaya Admin
                        </th>

                        <th class="text-center">
                            Diterima
                        </th>

                        <th class="text-center">
                            Piutang
                        </th>

                        <th class="text-center">
                            Total Bayar
                        </th>

                        <th class="text-center">
                            Fee
                        </th>

                        <th class="text-center">
                            Laba-Rugi
                        </th>

                        <th class="text-center">
                            Tgl Pembayaran
                        </th>

                    </tr>

                    </thead>


                    <tbody id="report-body"></tbody>


                    {{-- =================================================
                         FOOTER
                    ================================================== --}}

                    <tfoot>

                    <tr>

                        <th
                            class="text-center"
                            colspan="9"
                        >
                            Total
                        </th>

                        <th
                            class="text-center"
                            id="ttl_inv"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_ppn"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_pph"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_diskon"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_ongkir"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_biaya_admin"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_diterima"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_piutang"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_bayar"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_fee"
                        ></th>

                        <th
                            class="text-center"
                            id="ttl_laba"
                        ></th>

                        <th></th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Total Invoice
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="total-bersih"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Total Bersih
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="total-income"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Laba-Rugi
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="profit"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            PPN
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="ppn"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            PPH
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="pph"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Biaya Admin
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="biaya_admin"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Fee
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="fee"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Diskon
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="diskon"
                        >
                            Rp 0
                        </th>

                    </tr>


                    <tr>

                        <th
                            colspan="11"
                            class="text-center"
                        >
                            Ongkir
                        </th>

                        <th
                            colspan="10"
                            class="text-center"
                            id="ongkir"
                        >
                            Rp 0
                        </th>

                    </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>

@endsection


@push('head')

    <style>

        #filter-table ul {
            margin: 0;
            padding-left: 18px;
        }

        #filter-table li {
            margin-bottom: 2px;
        }

        #filter-table td {
            vertical-align: top;
        }

        .payment-item {
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #ddd;
        }

        .payment-item:last-child {
            border-bottom: none;
        }

    </style>

@endpush


@push('js')

    <script>

        $(document).ready(function () {


            /* ============================================================
               FORMAT RUPIAH
            ============================================================ */

            function formatRupiah(amount)
            {
                amount = parseFloat(amount);

                if (isNaN(amount)) {
                    amount = 0;
                }

                return 'Rp ' +
                    new Intl.NumberFormat('id-ID')
                        .format(amount);
            }


            /* ============================================================
               FORMAT DATE
            ============================================================ */

            function formatDate(dateString)
            {
                if (!dateString) {
                    return '-';
                }

                const date =
                    new Date(dateString);

                if (isNaN(date.getTime())) {
                    return dateString;
                }

                return date
                    .toLocaleDateString(
                        'id-ID',
                        {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }
                    )
                    .replace('Agt', 'Agt');
            }


            /* ============================================================
               FORMAT DATE INPUT
            ============================================================ */

            function formatDateInput(date)
            {
                const year =
                    date.getFullYear();

                const month =
                    String(
                        date.getMonth() + 1
                    ).padStart(2, '0');

                const day =
                    String(
                        date.getDate()
                    ).padStart(2, '0');

                return (
                    year +
                    '-' +
                    month +
                    '-' +
                    day
                );
            }


            /* ============================================================
               ESCAPE HTML
            ============================================================ */

            function escapeHtml(value)
            {
                if (
                    value === null ||
                    value === undefined
                ) {
                    return '';
                }

                return String(value)
                    .replace(
                        /&/g,
                        '&amp;'
                    )
                    .replace(
                        /</g,
                        '&lt;'
                    )
                    .replace(
                        />/g,
                        '&gt;'
                    )
                    .replace(
                        /"/g,
                        '&quot;'
                    )
                    .replace(
                        /'/g,
                        '&#039;'
                    );
            }


            /* ============================================================
               ITEM
            ============================================================ */

            function buildItemList(items)
            {
                let html =
                    '<ul>';

                if (
                    Array.isArray(items) &&
                    items.length > 0
                ) {

                    items.forEach(
                        function (item)
                        {

                            let name =
                                item.name ?? '';

                            let noSeri =
                                item.no_seri ?? '';

                            html += `
                        <li>
                            ${escapeHtml(name)}
                            ${
                                noSeri
                                    ? ' - (' +
                                    escapeHtml(noSeri) +
                                    ')'
                                    : ''
                            }
                        </li>
                    `;
                        }
                    );

                } else {

                    html +=
                        '<li>-</li>';
                }

                html +=
                    '</ul>';

                return html;
            }


            /* ============================================================
               ACCESSORIES
            ============================================================ */

            function buildAccessoriesList(
                accessories
            )
            {

                let html =
                    '<ul>';

                if (Array.isArray(accessories) && accessories.length > 0) {
                    accessories.forEach(function (accessory) {
                        let name = accessory.name ?? '';
                        let qty = parseFloat(accessory.qty ?? 0);
                        let price_sale = new Intl.NumberFormat('id-ID').format(
                            parseFloat(accessory.price_sale ?? 0)
                        );

                        if (isNaN(qty)) {
                            qty = 0;
                        }

                        html += `
                            <li>
                                ${escapeHtml(name)} <br>
                                Qty :${qty} <br> Harga :Rp. ${price_sale}
                                <hr>
                            </li>
                        `;
                    });
                }

                html +=
                    '</ul>';

                return html;
            }


            /* ============================================================
               PAYMENT
            ============================================================ */

            function buildPaymentList(
                debts
            )
            {

                let html =
                    '<div>';

                if (
                    Array.isArray(debts) &&
                    debts.length > 0
                ) {

                    debts.forEach(
                        function (debt)
                        {

                            const datePay =
                                debt.date_pay
                                    ? formatDate(
                                        debt.date_pay
                                    )
                                    : '-';


                            const bankName =
                                debt.bank?.name
                                ??
                                debt.bank_name
                                ??
                                '';


                            const description =
                                debt.description
                                ?? '';


                            let method =
                                debt.method
                                ?? '';


                            if (
                                !method &&
                                bankName
                            ) {
                                method =
                                    bankName;
                            }


                            if (
                                !method &&
                                !bankName &&
                                !description
                            ) {
                                method =
                                    'Tunai';
                            }


                            let amount =
                                parseFloat(
                                    debt.pay_debts ?? 0
                                );


                            if (isNaN(amount)) {
                                amount = 0;
                            }


                            const penerima =
                                debt.penerima ?? '';


                            let paymentMethod =
                                '';


                            if (bankName) {

                                paymentMethod = `
                            <strong>Metode:</strong>
                            ${escapeHtml(bankName)}
                        `;

                            } else if (description) {

                                paymentMethod = `
                            <strong>Keterangan:</strong>
                            ${escapeHtml(description)}
                        `;

                            } else {

                                paymentMethod =
                                    escapeHtml(
                                        method ||
                                        'Tunai'
                                    );
                            }


                            html += `
                        <div class="payment-item">

                            <strong>
                                ${escapeHtml(datePay)}
                            </strong>

                            <br>

                            ${paymentMethod}

                            <br>

                            ${formatRupiah(amount)}

                            ${
                                penerima
                                    ? '<br>Penerima: ' +
                                    escapeHtml(
                                        penerima
                                    )
                                    : ''
                            }

                        </div>
                    `;
                        }
                    );

                } else {

                    html += '-';
                }


                html +=
                    '</div>';

                return html;
            }


            /* ============================================================
               DATATABLE
            ============================================================ */

            var table =
                $('#filter-table').DataTable({

                    lengthChange: false,

                    paginate: false,

                    searching: true,

                    ordering: true,

                    autoWidth: false,

                    buttons: [

                        /* =================================================
                           EXCEL
                        ================================================= */

                        {
                            extend: 'excel',

                            title:
                                'Laporan Transaksi Manager',

                            text:
                                'Excel',

                            exportOptions: {

                                columns:
                                    ':visible',

                                stripHtml:
                                    false
                            },

                            filename: function ()
                            {

                                const today =
                                    new Date();

                                const yyyy =
                                    today.getFullYear();

                                const mm =
                                    String(
                                        today.getMonth() + 1
                                    ).padStart(2, '0');

                                const dd =
                                    String(
                                        today.getDate()
                                    ).padStart(2, '0');

                                return (
                                    'laporan transaksi manager ' +
                                    yyyy +
                                    '-' +
                                    mm +
                                    '-' +
                                    dd
                                );
                            },

                            customize: function (xlsx)
                            {

                                var sheet =
                                    xlsx.xl
                                        .worksheets[
                                        'sheet1.xml'
                                        ];

                                var styles =
                                    xlsx.xl[
                                        'styles.xml'
                                        ];

                                var $sheet =
                                    $(sheet);

                                var $styles =
                                    $(styles);


                                /* =================================================
                                   STYLE
                                ================================================= */

                                var cellXfs =
                                    $styles.find(
                                        'cellXfs'
                                    );

                                var xfCount =
                                    parseInt(
                                        cellXfs.attr(
                                            'count'
                                        )
                                    );


                                cellXfs.append(`
                            <xf
                                xfId="0"
                                applyAlignment="1"
                                applyFont="1"
                            >
                                <alignment
                                    vertical="center"
                                    wrapText="1"
                                />
                            </xf>
                        `);


                                cellXfs.attr(
                                    'count',
                                    xfCount + 1
                                );


                                var wrapStyle =
                                    xfCount;


                                /* =================================================
                                   FONT MERAH
                                ================================================= */

                                var fonts =
                                    $styles.find(
                                        'fonts'
                                    );

                                var fontCount =
                                    parseInt(
                                        fonts.attr(
                                            'count'
                                        )
                                    );


                                fonts.append(`
                            <font>
                                <sz val="11"/>
                                <color rgb="FFFF0000"/>
                                <name val="Calibri"/>
                            </font>
                        `);


                                fonts.attr(
                                    'count',
                                    fontCount + 1
                                );


                                var redFontId =
                                    fontCount;


                                var redStyle =
                                    parseInt(
                                        cellXfs.attr(
                                            'count'
                                        )
                                    );


                                cellXfs.append(`
                            <xf
                                xfId="0"
                                fontId="${redFontId}"
                                applyFont="1"
                                applyAlignment="1"
                            >
                                <alignment
                                    vertical="center"
                                    horizontal="center"
                                    wrapText="1"
                                />
                            </xf>
                        `);


                                cellXfs.attr(
                                    'count',
                                    redStyle + 1
                                );


                                /* =================================================
                                   CLEAN HTML
                                ================================================= */

                                function cleanHtmlToExcel(html)
                                {

                                    if (!html) {
                                        return '';
                                    }


                                    let temp =
                                        $('<div>')
                                            .html(html);


                                    temp.find(
                                        '.payment-item'
                                    ).each(
                                        function ()
                                        {

                                            $(this)
                                                .prepend(
                                                    '\n'
                                                );

                                            $(this)
                                                .append(
                                                    '\n'
                                                );
                                        }
                                    );


                                    temp.find('br')
                                        .replaceWith(
                                            '\n'
                                        );


                                    temp.find('li')
                                        .each(
                                            function ()
                                            {

                                                $(this)
                                                    .prepend(
                                                        '\n'
                                                    );

                                                $(this)
                                                    .append(
                                                        '\n'
                                                    );
                                            }
                                        );


                                    let text =
                                        temp.text();


                                    text =
                                        text
                                            .replace(
                                                /\r/g,
                                                ''
                                            )
                                            .replace(
                                                /\u00a0/g,
                                                ' '
                                            );


                                    text =
                                        text
                                            .replace(
                                                /\n\s*\n\s*\n+/g,
                                                '\n\n'
                                            )
                                            .replace(
                                                /[ \t]+\n/g,
                                                '\n'
                                            )
                                            .replace(
                                                /\n[ \t]+/g,
                                                '\n'
                                            )
                                            .trim();


                                    return text;
                                }


                                /* =================================================
                                   CLEAN CELL
                                ================================================= */

                                $('row c', sheet)
                                    .each(
                                        function ()
                                        {

                                            var cell =
                                                $(this);

                                            var inlineText =
                                                cell.find(
                                                    'is t'
                                                );


                                            if (
                                                inlineText.length === 0
                                            ) {
                                                return;
                                            }


                                            var html =
                                                inlineText.html();


                                            var decoded =
                                                $('<textarea/>')
                                                    .html(html)
                                                    .text();


                                            var cleanText =
                                                cleanHtmlToExcel(
                                                    decoded
                                                );


                                            inlineText.text(
                                                cleanText
                                            );


                                            cell.attr(
                                                's',
                                                wrapStyle
                                            );
                                        }
                                    );


                                /* =================================================
                                   COLUMN WIDTH
                                ================================================= */

                                var cols =
                                    $sheet.find(
                                        'cols'
                                    );


                                if (
                                    cols.length === 0
                                ) {

                                    $sheet.find(
                                        'sheetFormatPr'
                                    ).after(
                                        '<cols></cols>'
                                    );

                                    cols =
                                        $sheet.find(
                                            'cols'
                                        );
                                }


                                cols.empty();


                                cols.append(`
                            <col
                                min="1"
                                max="1"
                                width="6"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="2"
                                max="2"
                                width="15"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="3"
                                max="3"
                                width="20"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="4"
                                max="4"
                                width="22"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="5"
                                max="5"
                                width="25"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="6"
                                max="6"
                                width="22"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="7"
                                max="7"
                                width="25"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="8"
                                max="8"
                                width="30"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="9"
                                max="9"
                                width="12"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="10"
                                max="20"
                                width="18"
                                customWidth="1"
                            />
                        `);


                                cols.append(`
                            <col
                                min="21"
                                max="21"
                                width="17"
                                customWidth="1"
                            />
                        `);


                                /* =================================================
                                   ROW HEIGHT
                                ================================================= */

                                $sheet
                                    .find(
                                        'sheetData row'
                                    )
                                    .each(
                                        function ()
                                        {

                                            var row =
                                                $(this);

                                            var rowNumber =
                                                row.attr(
                                                    'r'
                                                );


                                            if (
                                                rowNumber == 1
                                            ) {

                                                row.attr(
                                                    'ht',
                                                    '15'
                                                );

                                                row.attr(
                                                    'customHeight',
                                                    '1'
                                                );

                                                return;
                                            }


                                            var maxLines =
                                                1;


                                            row.find(
                                                'c is t'
                                            ).each(
                                                function ()
                                                {

                                                    var text =
                                                        $(this)
                                                            .text();


                                                    if (text) {

                                                        var lines =
                                                            text.split(
                                                                '\n'
                                                            ).length;


                                                        if (
                                                            lines >
                                                            maxLines
                                                        ) {

                                                            maxLines =
                                                                lines;
                                                        }
                                                    }
                                                }
                                            );


                                            var height =
                                                Math.max(
                                                    10,
                                                    Math.min(
                                                        maxLines * 14,
                                                        95
                                                    )
                                                );


                                            row.attr(
                                                'ht',
                                                height
                                            );

                                            row.attr(
                                                'customHeight',
                                                '1'
                                            );
                                        }
                                    );


                                /* =================================================
                                   FOOTER
                                ================================================= */

                                function getFooterText(id)
                                {

                                    const el =
                                        document.getElementById(
                                            id
                                        );

                                    return el
                                        ? el.innerText
                                        : '0';
                                }


                                var lastRow = 0;


                                $sheet
                                    .find(
                                        'sheetData row'
                                    )
                                    .each(
                                        function ()
                                        {

                                            var r =
                                                parseInt(
                                                    $(this)
                                                        .attr(
                                                            'r'
                                                        )
                                                );


                                            if (
                                                r > lastRow
                                            ) {
                                                lastRow = r;
                                            }
                                        }
                                    );


                                var rowStart =
                                    lastRow + 2;


                                function addFooterRow(
                                    label,
                                    value
                                )
                                {

                                    var rowNumber =
                                        rowStart++;


                                    var safeLabel =
                                        String(
                                            label ?? ''
                                        )
                                            .replace(
                                                /&/g,
                                                '&amp;'
                                            )
                                            .replace(
                                                /</g,
                                                '&lt;'
                                            )
                                            .replace(
                                                />/g,
                                                '&gt;'
                                            )
                                            .replace(
                                                /"/g,
                                                '&quot;'
                                            )
                                            .replace(
                                                /'/g,
                                                '&apos;'
                                            );


                                    var safeValue =
                                        String(
                                            value ?? ''
                                        )
                                            .replace(
                                                /&/g,
                                                '&amp;'
                                            )
                                            .replace(
                                                /</g,
                                                '&lt;'
                                            )
                                            .replace(
                                                />/g,
                                                '&gt;'
                                            )
                                            .replace(
                                                /"/g,
                                                '&quot;'
                                            )
                                            .replace(
                                                /'/g,
                                                '&apos;'
                                            );


                                    $sheet
                                        .find(
                                            'sheetData'
                                        )
                                        .append(`

                                    <row
                                        r="${rowNumber}"
                                        ht="22"
                                        customHeight="1"
                                    >

                                        <c
                                            t="inlineStr"
                                            r="C${rowNumber}"
                                            s="${wrapStyle}"
                                        >
                                            <is>
                                                <t>${safeLabel}</t>
                                            </is>
                                        </c>

                                        <c
                                            t="inlineStr"
                                            r="D${rowNumber}"
                                            s="${wrapStyle}"
                                        >
                                            <is>
                                                <t>${safeValue}</t>
                                            </is>
                                        </c>

                                    </row>
                                `);
                                }


                                /* =================================================
                                   TOTAL UTAMA
                                ================================================= */

                                function addTotalRow()
                                {

                                    var rowNumber =
                                        rowStart++;


                                    var values = [

                                        '',

                                        '',

                                        '',

                                        '',

                                        '',

                                        '',

                                        '',

                                        '',

                                        'Total',

                                        getFooterText(
                                            'ttl_inv'
                                        ),

                                        getFooterText(
                                            'ttl_ppn'
                                        ),

                                        getFooterText(
                                            'ttl_pph'
                                        ),

                                        getFooterText(
                                            'ttl_diskon'
                                        ),

                                        getFooterText(
                                            'ttl_ongkir'
                                        ),

                                        getFooterText(
                                            'ttl_biaya_admin'
                                        ),

                                        getFooterText(
                                            'ttl_diterima'
                                        ),

                                        getFooterText(
                                            'ttl_piutang'
                                        ),

                                        getFooterText(
                                            'ttl_bayar'
                                        ),

                                        getFooterText(
                                            'ttl_fee'
                                        ),

                                        getFooterText(
                                            'ttl_laba'
                                        ),

                                        ''

                                    ];


                                    var cells = '';


                                    values.forEach(
                                        function (
                                            value,
                                            index
                                        )
                                        {

                                            var columnNumber =
                                                index + 1;


                                            var columnName =
                                                '';


                                            var n =
                                                columnNumber;


                                            while (
                                                n > 0
                                                ) {

                                                var remainder =
                                                    (
                                                        n - 1
                                                    ) % 26;


                                                columnName =
                                                    String.fromCharCode(
                                                        65 +
                                                        remainder
                                                    ) +
                                                    columnName;


                                                n =
                                                    Math.floor(
                                                        (
                                                            n - 1
                                                        ) / 26
                                                    );
                                            }


                                            var safeValue =
                                                String(
                                                    value ?? ''
                                                )
                                                    .replace(
                                                        /&/g,
                                                        '&amp;'
                                                    )
                                                    .replace(
                                                        /</g,
                                                        '&lt;'
                                                    )
                                                    .replace(
                                                        />/g,
                                                        '&gt;'
                                                    )
                                                    .replace(
                                                        /"/g,
                                                        '&quot;'
                                                    )
                                                    .replace(
                                                        /'/g,
                                                        '&apos;'
                                                    );


                                            cells += `

                                        <c
                                            t="inlineStr"
                                            r="${columnName}${rowNumber}"
                                            s="${redStyle}"
                                        >

                                            <is>

                                                <t>
                                                    ${safeValue}
                                                </t>

                                            </is>

                                        </c>

                                    `;
                                        }
                                    );


                                    $sheet
                                        .find(
                                            'sheetData'
                                        )
                                        .append(`

                                    <row
                                        r="${rowNumber}"
                                        ht="22"
                                        customHeight="1"
                                    >

                                        ${cells}

                                    </row>

                                `);
                                }


                                addTotalRow();


                                /* =================================================
                                   FOOTER RINGKASAN
                                ================================================= */

                                addFooterRow(
                                    'Total Invoice',
                                    getFooterText(
                                        'total-bersih'
                                    )
                                );


                                addFooterRow(
                                    'Total Bersih',
                                    getFooterText(
                                        'total-income'
                                    )
                                );


                                addFooterRow(
                                    'Laba-Rugi',
                                    getFooterText(
                                        'profit'
                                    )
                                );


                                addFooterRow(
                                    'PPN',
                                    getFooterText(
                                        'ppn'
                                    )
                                );


                                addFooterRow(
                                    'PPH',
                                    getFooterText(
                                        'pph'
                                    )
                                );


                                addFooterRow(
                                    'Biaya Admin',
                                    getFooterText(
                                        'biaya_admin'
                                    )
                                );


                                addFooterRow(
                                    'Fee',
                                    getFooterText(
                                        'fee'
                                    )
                                );


                                addFooterRow(
                                    'Diskon',
                                    getFooterText(
                                        'diskon'
                                    )
                                );


                                addFooterRow(
                                    'Ongkir',
                                    getFooterText(
                                        'ongkir'
                                    )
                                );
                            }
                        },


                        /* =================================================
                           PDF
                        ================================================= */

                        {
                            extend: 'pdf',

                            text: 'PDF',

                            exportOptions: {

                                page: 'all',

                                columns:
                                    ':visible'
                            },

                            filename: function ()
                            {

                                const today =
                                    new Date();

                                const yyyy =
                                    today.getFullYear();

                                const mm =
                                    String(
                                        today.getMonth() + 1
                                    ).padStart(
                                        2,
                                        '0'
                                    );

                                const dd =
                                    String(
                                        today.getDate()
                                    ).padStart(
                                        2,
                                        '0'
                                    );

                                return (
                                    'laporan transaksi manager ' +
                                    yyyy +
                                    '-' +
                                    mm +
                                    '-' +
                                    dd
                                );
                            },

                            customize: function (doc)
                            {

                                doc.pageSize =
                                    'A4';

                                doc.pageOrientation =
                                    'landscape';

                                doc.pageMargins =
                                    [
                                        20,
                                        20,
                                        20,
                                        20
                                    ];


                                let headers = [];

                                let widths = [];


                                $('#filter-table thead th')
                                    .each(
                                        function (index)
                                        {

                                            let headerText =
                                                $(this)
                                                    .text()
                                                    .trim();


                                            headers.push({

                                                text:
                                                headerText,

                                                style:
                                                    'tableHeader'

                                            });


                                            if (
                                                index === 0
                                            ) {

                                                widths.push(
                                                    15
                                                );

                                            } else if (
                                                [
                                                    'Total Invoice',
                                                    'Diskon',
                                                    'Ongkir',
                                                    'Tanggal',
                                                    'PPH',
                                                    'PPN',
                                                    'Diterima',
                                                    'Piutang',
                                                    'Fee',
                                                    'Laba-Rugi',
                                                    'Total Bayar'
                                                ].includes(
                                                    headerText
                                                )
                                            ) {

                                                widths.push(
                                                    30
                                                );

                                            } else if (
                                                headerText ===
                                                'Invoice'
                                            ) {

                                                widths.push(
                                                    50
                                                );

                                            } else if (
                                                headerText ===
                                                'Divisi'
                                            ) {

                                                widths.push(
                                                    35
                                                );

                                            } else if (
                                                headerText ===
                                                'Total Item'
                                            ) {

                                                widths.push(
                                                    20
                                                );

                                            } else {

                                                widths.push(
                                                    '*'
                                                );
                                            }

                                        }
                                    );


                                let tableBody = [];


                                tableBody.push(
                                    headers
                                );


                                $('#filter-table tbody tr')
                                    .each(
                                        function ()
                                        {

                                            let row = [];


                                            $(this)
                                                .find('td')
                                                .each(
                                                    function ()
                                                    {

                                                        let htmlContent =
                                                            $(this)
                                                                .html();


                                                        let cleanedHtml =
                                                            htmlContent
                                                                .replace(
                                                                    /<\/?(ul|li|div)>/gi,
                                                                    ' '
                                                                )
                                                                .replace(
                                                                    /<br\s*\/?>/gi,
                                                                    ' '
                                                                )
                                                                .replace(
                                                                    /\s+/g,
                                                                    ' '
                                                                )
                                                                .trim();


                                                        let cleanText =
                                                            $('<div>')
                                                                .html(
                                                                    cleanedHtml
                                                                )
                                                                .text()
                                                                .trim();


                                                        row.push({

                                                            text:
                                                            cleanText,

                                                            style:
                                                                'tableCell'

                                                        });

                                                    }
                                                );


                                            while (
                                                row.length <
                                                headers.length
                                                ) {

                                                row.push({

                                                    text:
                                                        '',

                                                    style:
                                                        'tableCell'

                                                });
                                            }


                                            tableBody.push(
                                                row
                                            );
                                        }
                                    );


                                /* =================================================
                                   FOOTER PDF
                                ================================================= */

                                $('#filter-table tfoot tr')
                                    .each(
                                        function ()
                                        {

                                            let row = [];


                                            $(this)
                                                .find(
                                                    'th, td'
                                                )
                                                .each(
                                                    function ()
                                                    {

                                                        let text =
                                                            $(this)
                                                                .text()
                                                                .trim();


                                                        row.push({

                                                            text:
                                                            text,

                                                            style:
                                                                'tableCell'

                                                        });

                                                    }
                                                );


                                            while (
                                                row.length <
                                                headers.length
                                                ) {

                                                row.push({

                                                    text:
                                                        '',

                                                    style:
                                                        'tableCell'

                                                });
                                            }


                                            tableBody.push(
                                                row
                                            );
                                        }
                                    );


                                doc.content = [

                                    {

                                        table: {

                                            headerRows:
                                                1,

                                            widths:
                                            widths,

                                            body:
                                            tableBody

                                        },

                                        layout: {

                                            hLineWidth:
                                                function () {
                                                    return 0.5;
                                                },

                                            vLineWidth:
                                                function () {
                                                    return 0.5;
                                                },

                                            hLineColor:
                                                function () {
                                                    return '#aaa';
                                                },

                                            vLineColor:
                                                function () {
                                                    return '#aaa';
                                                },

                                            paddingLeft:
                                                function () {
                                                    return 4;
                                                },

                                            paddingRight:
                                                function () {
                                                    return 4;
                                                },

                                            paddingTop:
                                                function () {
                                                    return 3;
                                                },

                                            paddingBottom:
                                                function () {
                                                    return 3;
                                                }

                                        }

                                    }

                                ];


                                doc.styles = {

                                    tableHeader: {

                                        bold:
                                            true,

                                        fontSize:
                                            4,

                                        fillColor:
                                            '#eeeeee',

                                        alignment:
                                            'center'

                                    },

                                    tableCell: {

                                        fontSize:
                                            3,

                                        alignment:
                                            'left'

                                    }

                                };

                            }

                        },


                        /* =================================================
                           PRINT
                        ================================================= */

                        {

                            extend:
                                'print',

                            text:
                                'Print',

                            exportOptions: {

                                stripHtml:
                                    false,

                                columns:
                                    ':visible'

                            },

                            customize:
                                function (win)
                                {

                                    const css =
                                        '@page { size: landscape; margin: 10mm; }';


                                    const head =
                                        win.document.head ||
                                        win.document
                                            .getElementsByTagName(
                                                'head'
                                            )[0];


                                    const style =
                                        win.document
                                            .createElement(
                                                'style'
                                            );


                                    style.type =
                                        'text/css';

                                    style.media =
                                        'print';


                                    style.appendChild(

                                        win.document
                                            .createTextNode(
                                                css
                                            )

                                    );


                                    head.appendChild(
                                        style
                                    );


                                    $(win.document.body)
                                        .css(
                                            'font-size',
                                            '10px'
                                        );


                                    const $table =
                                        $(win.document.body)
                                            .find(
                                                'table'
                                            );


                                    $table
                                        .addClass(
                                            'compact'
                                        )
                                        .css({

                                            'font-size':
                                                '10px',

                                            'border-collapse':
                                                'collapse',

                                            'width':
                                                '100%'

                                        });


                                    $table
                                        .find('tbody')
                                        .remove();


                                    $table
                                        .find('tfoot')
                                        .remove();


                                    const tbody =
                                        $('#filter-table tbody')
                                            .clone();


                                    $table.append(
                                        tbody
                                    );


                                    const footerHtml =
                                        $('<div>')
                                            .css({

                                                'margin-top':
                                                    '20px',

                                                'font-size':
                                                    '10px'

                                            })
                                            .append(
                                                $('#filter-table tfoot')
                                                    .clone()
                                            );


                                    $(win.document.body)
                                        .append(
                                            footerHtml
                                        );

                                }

                        }

                    ]

                });


            /* ============================================================
               DATATABLE BUTTON
            ============================================================ */

            table
                .buttons()
                .container()
                .appendTo(
                    '#filter-table_wrapper .col-md-6:eq(0)'
                );


            /* ============================================================
               LOAD DATA
            ============================================================ */

            function loadData(
                startDate = '',
                endDate = '',
                divisiId = 'all'
            )
            {

                $.ajax({

                    url:
                        '{{ route("manager.report.filter") }}',

                    method:
                        'GET',

                    data: {

                        start_date:
                        startDate,

                        end_date:
                        endDate,

                        divisi_id:
                        divisiId

                    },


                    /* =====================================================
                       BEFORE SEND
                    ===================================================== */

                    beforeSend:
                        function ()
                        {

                            $('#filter-btn')
                                .prop(
                                    'disabled',
                                    true
                                )
                                .html(
                                    '<i class="bx bx-loader-alt bx-spin"></i> Loading...'
                                );

                        },


                    /* =====================================================
                       SUCCESS
                    ===================================================== */

                    success:
                        function (response)
                        {

                            if (response.error) {

                                alert(
                                    response.error
                                );

                                return;
                            }


                            /* =============================================
                               CLEAR
                            ============================================== */

                            table.clear();


                            /* =============================================
                               FOOTER
                            ============================================== */

                            const footer =
                                response.footer ?? {};


                            $('#total-income')
                                .text(
                                    formatRupiah(
                                        response.income ?? 0
                                    )
                                );


                            $('#profit')
                                .text(
                                    formatRupiah(
                                        response.profit ?? 0
                                    )
                                );


                            $('#diskon')
                                .text(
                                    formatRupiah(
                                        response.diskon ?? 0
                                    )
                                );


                            $('#ongkir')
                                .text(
                                    formatRupiah(
                                        response.ongkir ?? 0
                                    )
                                );


                            $('#ppn')
                                .text(
                                    formatRupiah(
                                        response.ppn ?? 0
                                    )
                                );


                            $('#pph')
                                .text(
                                    formatRupiah(
                                        response.pph ?? 0
                                    )
                                );


                            $('#biaya_admin')
                                .text(
                                    formatRupiah(
                                        response.admin ?? 0
                                    )
                                );


                            $('#fee')
                                .text(
                                    formatRupiah(
                                        response.fee ?? 0
                                    )
                                );


                            $('#total-bersih')
                                .text(
                                    formatRupiah(
                                        response.totalprice ?? 0
                                    )
                                );


                            /* =============================================
                               FOOTER TABLE
                            ============================================== */

                            $('#ttl_inv')
                                .text(
                                    formatRupiah(
                                        footer.total_invoice ?? 0
                                    )
                                );


                            $('#ttl_ppn')
                                .text(
                                    formatRupiah(
                                        footer.ppn ?? 0
                                    )
                                );


                            $('#ttl_pph')
                                .text(
                                    formatRupiah(
                                        footer.pph ?? 0
                                    )
                                );


                            $('#ttl_diskon')
                                .text(
                                    formatRupiah(
                                        footer.diskon ?? 0
                                    )
                                );


                            $('#ttl_ongkir')
                                .text(
                                    formatRupiah(
                                        footer.ongkir ?? 0
                                    )
                                );


                            $('#ttl_biaya_admin')
                                .text(
                                    formatRupiah(
                                        footer.admin ?? 0
                                    )
                                );


                            $('#ttl_diterima')
                                .text(
                                    formatRupiah(
                                        footer.diterima ?? 0
                                    )
                                );


                            $('#ttl_piutang')
                                .text(
                                    formatRupiah(
                                        footer.piutang ?? 0
                                    )
                                );


                            $('#ttl_bayar')
                                .text(
                                    formatRupiah(
                                        footer.total_bayar ?? 0
                                    )
                                );


                            $('#ttl_fee')
                                .text(
                                    formatRupiah(
                                        footer.fee ?? 0
                                    )
                                );


                            $('#ttl_laba')
                                .text(
                                    formatRupiah(
                                        footer.laba ?? 0
                                    )
                                );


                            /* =============================================
                               REPORT
                            ============================================== */

                            if (
                                Array.isArray(
                                    response.report
                                )
                            ) {

                                response.report.forEach(
                                    function (
                                        data,
                                        index
                                    )
                                    {

                                        let itemSalesList =
                                            buildItemList(
                                                data.itemSales
                                            );


                                        let accessoriesList =
                                            buildAccessoriesList(
                                                data.accessories
                                            );


                                        let debtList =
                                            buildPaymentList(
                                                data.debt
                                            );


                                        let totalInvoice =
                                            parseFloat(
                                                data.total_price ?? 0
                                            );


                                        let ppn =
                                            parseFloat(
                                                data.ppn ?? 0
                                            );


                                        let pph =
                                            parseFloat(
                                                data.pph ?? 0
                                            );


                                        let diskon =
                                            parseFloat(
                                                data.diskon ?? 0
                                            );


                                        let ongkir =
                                            parseFloat(
                                                data.ongkir ?? 0
                                            );


                                        let admin =
                                            parseFloat(
                                                data.admin_fee ?? 0
                                            );


                                        let diterima =
                                            parseFloat(
                                                data.nominal_in ?? 0
                                            );


                                        let piutang =
                                            parseFloat(
                                                data.piutang ?? 0
                                            );


                                        let totalBayar =
                                            parseFloat(
                                                data.pay ?? 0
                                            );


                                        let fee =
                                            parseFloat(
                                                data.fee ?? 0
                                            );


                                        let laba =
                                            parseFloat(
                                                data.profit ?? 0
                                            );


                                        table.row.add([

                                            index + 1,

                                            formatDate(
                                                data.created_at ?? ''
                                            ),

                                            escapeHtml(
                                                data.divisi ?? 'N/A'
                                            ),

                                            escapeHtml(
                                                data.inv_manual ?? ''
                                            ),

                                            escapeHtml(
                                                data.invoice ?? 'N/A'
                                            ),

                                            escapeHtml(
                                                data.customer?.name ??
                                                'N/A'
                                            ),

                                            itemSalesList,

                                            accessoriesList,

                                            data.total_item ?? 0,

                                            formatRupiah(
                                                totalInvoice
                                            ),

                                            formatRupiah(
                                                ppn
                                            ),

                                            formatRupiah(
                                                pph
                                            ),

                                            formatRupiah(
                                                diskon
                                            ),

                                            formatRupiah(
                                                ongkir
                                            ),

                                            formatRupiah(
                                                admin
                                            ),

                                            formatRupiah(
                                                diterima
                                            ),

                                            formatRupiah(
                                                piutang
                                            ),

                                            formatRupiah(
                                                totalBayar
                                            ),

                                            formatRupiah(
                                                fee
                                            ),

                                            formatRupiah(
                                                laba
                                            ),

                                            debtList

                                        ]);

                                    }
                                );
                            }


                            /* =============================================
                               DRAW
                            ============================================== */

                            table.draw(false);

                        },


                    /* =====================================================
                       ERROR
                    ====================================================== */

                    error:
                        function (xhr)
                        {

                            console.error(
                                'REPORT ERROR:',
                                xhr.responseText
                            );


                            let message =
                                'Terjadi kesalahan saat memproses laporan.';


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.message
                            ) {

                                message =
                                    xhr.responseJSON.message;
                            }


                            alert(
                                message
                            );

                        },


                    /* =====================================================
                       COMPLETE
                    ====================================================== */

                    complete:
                        function ()
                        {

                            $('#filter-btn')
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html(
                                    '<i class="bx bx-filter"></i> Filter'
                                );

                        }

                });

            }


            /* ============================================================
               LOAD PERTAMA
            ============================================================ */

            loadData(
                '',
                '',
                'all'
            );


            /* ============================================================
               FILTER
            ============================================================ */

            $('#filter-btn')
                .on(
                    'click',
                    function ()
                    {

                        let startDate =
                            $('#starDate').val();


                        let endDate =
                            $('#endDate').val();


                        let divisiId =
                            $('#divisi_id').val();


                        if (
                            startDate &&
                            endDate &&
                            startDate > endDate
                        ) {

                            alert(
                                'Tanggal mulai tidak boleh lebih besar dari tanggal berakhir.'
                            );

                            return;
                        }


                        loadData(
                            startDate,
                            endDate,
                            divisiId
                        );

                    }
                );


            /* ============================================================
               RESET
            ============================================================ */

            $('#reset-btn')
                .on(
                    'click',
                    function ()
                    {

                        const today =
                            new Date();


                        const firstDay =
                            new Date(
                                today.getFullYear(),
                                today.getMonth(),
                                1
                            );


                        const startDate =
                            formatDateInput(
                                firstDay
                            );


                        const endDate =
                            formatDateInput(
                                today
                            );


                        $('#starDate')
                            .val(
                                startDate
                            );


                        $('#endDate')
                            .val(
                                endDate
                            );


                        $('#divisi_id')
                            .val(
                                'all'
                            );


                        loadData(
                            startDate,
                            endDate,
                            'all'
                        );

                    }
                );

        });

    </script>

@endpush
