@extends('layouts.master')

@section('title', 'TRANSAKSI BARU')

@section('content')

    <style>
        /* =========================================================
           SALE CARD
        ========================================================= */
        .sale-wrapper {
            background: #f5f6fa;
            padding: 15px;
        }

        .sale-card,
        .payment-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .sale-card-header,
        .payment-header {
            padding: 14px 18px;
            border-bottom: 1px solid #dee2e6;
            font-size: 17px;
            font-weight: 600;
            color: #343a40;
        }

        .sale-card-body,
        .payment-body {
            padding: 18px;
        }

        /* =========================================================
           DIVISI
        ========================================================= */
        .division-wrapper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .division-title {
            font-size: 16px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }

        .division-required {
            color: #dc3545;
        }

        #divisionSelect {
            width: 100%;
        }

        .division-warning {
            margin-top: 8px;
            font-size: 12px;
            color: #dc3545;
            display: none;
        }

        /* =========================================================
           PRODUCT TABLE
        ========================================================= */
        .product-table th {
            white-space: nowrap;
            vertical-align: middle;
            font-size: 13px;
        }

        .product-table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .price-input,
        .stok-input {
            min-width: 90px;
            height: 38px;
            font-size: 13px;
        }

        .btn-delete-row {
            min-width: 38px;
        }

        /* =========================================================
           BARCODE
        ========================================================= */
        #code {
            height: 42px;
            font-size: 14px;
        }

        #productSelect {
            width: 100%;
        }

        .barcode-disabled {
            opacity: 0.65;
        }

        /* =========================================================
           PAYMENT GRID
        ========================================================= */
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 15px;
        }

        .payment-field {
            min-width: 0;
        }

        .payment-field label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: 500;
            color: #343a40;
        }

        .payment-field .form-control {
            height: 38px;
            border-radius: 6px;
            font-size: 13px;
            padding: 6px 10px;
        }

        /* =========================================================
           TOTAL
        ========================================================= */
        .payment-total,
        .payment-grand-total {
            background: #f8f9fa;
            font-weight: 600;
        }

        #kembalian {
            background: #f8f9fa;
            font-weight: 600;
        }

        /* =========================================================
           PAYMENT METHOD
        ========================================================= */
        .payment-method-wrapper {
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .payment-method-title {
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }

        .payment-method-options {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .payment-method-option {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            cursor: pointer;
            font-size: 13px;
            color: #343a40;
        }

        .payment-method-option input {
            width: 15px;
            height: 15px;
            margin: 0;
            cursor: pointer;
        }

        .payment-method-option i {
            font-size: 16px;
        }

        /* =========================================================
           TRANSFER
        ========================================================= */
        .transfer-fields {
            margin-top: 14px;
        }

        .transfer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 15px;
        }

        /* =========================================================
           BUTTON
        ========================================================= */
        .save-wrapper {
            margin-top: 18px;
        }

        .save-wrapper .btn {
            height: 40px;
            border-radius: 6px;
            font-size: 13px;
        }

        /* =========================================================
           MOBILE
        ========================================================= */
        @media (max-width: 768px) {
            .payment-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .transfer-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .sale-card-body,
            .payment-body {
                padding: 14px;
            }

            .payment-method-options {
                gap: 15px;
            }
        }

        .realtime-clock {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .clock-box {
            display: flex;
            background: #ff8600;
            border-radius: 4px;
            overflow: hidden;
            color: #fff;
            min-width: 260px;
        }

        .clock-item {
            flex: 1;
            text-align: center;
            padding: 10px 18px;
            font-size: 30px;
            font-weight: 500;
            line-height: 1;
            border-right: 1px solid rgba(255, 255, 255, .5);
        }

        .clock-item:last-child {
            border-right: none;
        }

        @media (max-width: 768px) {
            .realtime-clock {
                justify-content: center;
            }

            .clock-box {
                width: 100%;
                max-width: 260px;
            }

            .clock-item {
                font-size: 24px;
                padding: 9px 12px;
            }
        }
    </style>


    {{-- =========================================================
         PILIH DIVISI
    ========================================================== --}}
    <div class="division-wrapper">

        <div class="row">
            <div class="col-sm-6">
                <div class="division-title">
                    Divisi
                    <span class="division-required">*</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="realtime-clock">
                    <div class="clock-box">
                        <div class="clock-item" id="clockHour">00</div>
                        <div class="clock-item" id="clockMinute">00</div>
                        <div class="clock-item" id="clockSecond">00</div>
                    </div>
                </div>
            </div>
        </div>
        <select
            name="divisi_id"
            id="divisionSelect"
            class="form-control"
            data-placeholder="-- Pilih Divisi Terlebih Dahulu --">

            <option value="">
                -- Pilih Divisi Terlebih Dahulu --
            </option>

            @foreach($divisi as $data)

                <option
                    value="{{ $data->id }}"
                    data-inv-format="{{ $data->inv_format }}">

                    {{ $data->name }}

                </option>

            @endforeach

        </select>

        <div id="divisionWarning" class="division-warning">
            Silakan pilih divisi terlebih dahulu sebelum melakukan transaksi.
        </div>

    </div>


    <div class="sale-wrapper">

        <div class="row g-3">

            {{-- =========================================================
                 DETAIL TRANSAKSI
            ========================================================== --}}
            <div class="col-lg-8">

                <div class="sale-card">

                    <div class="sale-card-header">
                        Detail Transaksi
                    </div>

                    <div class="sale-card-body">

                        {{-- =================================================
                             INVOICE + CUSTOMER
                        ================================================== --}}
                        <div class="row g-3 mb-3">

                            <div class="col-md-6">

                                <label for="invoice" class="form-label">
                                    Invoice
                                </label>

                                <select
                                    name="kode"
                                    id="invoice"
                                    class="form-control"
                                    disabled>

                                    <option value="">
                                        Pilih Divisi Terlebih Dahulu
                                    </option>

                                    @foreach($divisi as $data)

                                        <option
                                            value="{{ $data->inv_format }}"
                                            data-divisi-id="{{ $data->id }}">

                                            {{ $data->inv_format }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="single-select-field"
                                    class="form-label">

                                    Customer

                                </label>

                                <select
                                    name="customer_id"
                                    id="single-select-field"
                                    class="form-control"
                                    disabled>

                                    <option value="">
                                        -- Pilih Divisi Terlebih Dahulu --
                                    </option>

                                    @foreach($customer as $data)

                                        <option
                                            value="{{ $data->id }}"
                                            data-divisi-id="{{ $data->divisi_id }}">

                                            {{ $data->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- =================================================
                             TANGGAL TRANSAKSI
                        ================================================== --}}
                        <div class="row g-3 mb-3">

                            <div class="col-md-6">

                                <label
                                    for="created_at"
                                    class="form-label">

                                    Tanggal Transaksi

                                </label>

                                <input
                                    type="text"
                                    class="form-control datepicker"
                                    name="created_at"
                                    id="created_at"
                                    value="{{ old('created_at') }}"
                                    placeholder="Tanggal Invoice">

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="no_po"
                                    class="form-label">

                                    Nomor PO

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="no_po"
                                    id="no_po"
                                    placeholder="Nomor PO">

                            </div>

                        </div>


                        {{-- =================================================
                             TANGGAL PEMBAYARAN
                        ================================================== --}}
                        <div class="row g-3 mb-3">

                            <div class="col-md-6">

                                <label
                                    for="date_pay"
                                    class="form-label">

                                    Tanggal Pembayaran

                                </label>

                                <input
                                    type="text"
                                    class="form-control datepicker"
                                    name="date_pay"
                                    id="date_pay"
                                    placeholder="Tanggal Pembayaran">

                            </div>

                        </div>


                        {{-- =================================================
                             SCAN BARCODE
                        ================================================== --}}
                        <div class="mb-2">

                            <label
                                for="code"
                                class="form-label">

                                Scan Barcode

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bx bx-barcode"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="code"
                                    id="code"
                                    placeholder="Pilih divisi terlebih dahulu..."
                                    autocomplete="off"
                                    disabled>

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    id="btnSearchCode"
                                    disabled>

                                    <i class="bx bx-search"></i>
                                    Cari

                                </button>

                            </div>

                            <small class="text-muted">
                                Pilih divisi terlebih dahulu sebelum melakukan scan barcode.
                            </small>

                        </div>


                        {{-- =========================================================
      PILIH PRODUK
 ========================================================= --}}
                        <div class="mb-3">

                            <label for="productSelect" class="form-label">
                                Atau Pilih Produk
                            </label>

                            <select
                                id="productSelect"
                                class="form-control"
                                disabled>

                                <option value="">
                                    -- Pilih Divisi Terlebih Dahulu --
                                </option>


                                {{-- =====================================================
                                     ACCESSORIES
                                ====================================================== --}}
                                @if(isset($accessories))

                                    <optgroup label="ACCESSORIES">

                                        @foreach($accessories as $accessory)

                                            {{-- HANYA STOK DI ATAS 0 --}}
                                            @if($accessory->stok > 0)

                                                <option
                                                    value="accessory|{{ $accessory->id }}"
                                                    data-type="accessory"
                                                    data-id="{{ $accessory->id }}"
                                                    data-divisi-id="{{ $accessory->divisi_id }}"
                                                    data-code="{{ $accessory->code_acces }}"
                                                    data-name="{{ $accessory->name }}"
                                                    data-price="{{ $accessory->price }}"
                                                    data-price-bottom="{{ $accessory->price_bottom }}"
                                                    data-stok="{{ $accessory->stok }}">

                                                    {{ $accessory->code_acces }}
                                                    -
                                                    {{ $accessory->name }}
                                                    -
                                                    Stok: {{ $accessory->stok }}

                                                </option>

                                            @endif

                                        @endforeach

                                    </optgroup>

                                @endif


                                {{-- =====================================================
                                     ITEM / ALAT

                                     Controller menggunakan:
                                     $item = Item::all();

                                     Jadi Blade harus menggunakan $item
                                ====================================================== --}}
                                @if(isset($item))

                                    <optgroup label="ALAT / ITEM">

                                        @foreach($item as $data)

                                            <option
                                                value="item|{{ $data->id }}"
                                                data-type="item"
                                                data-id="{{ $data->id }}"
                                                data-divisi-id="{{ $data->divisi_id }}"
                                                data-no-seri="{{ $data->no_seri }}"
                                                data-name="{{ $data->name }}"
                                                data-price="{{ $data->price }}"
                                                data-price-bottom="{{ $data->price_bottom }}"
                                                data-itemcategory-id="{{ $data->itemcategory_id }}"
                                                data-capital-price="{{ $data->capital_price }}"
                                                data-created-at="{{ $data->created_at }}">

                                                {{ $data->no_seri }}
                                                -
                                                {{ $data->name }}

                                            </option>

                                        @endforeach

                                    </optgroup>

                                @endif

                            </select>

                        </div>
                        {{-- =================================================
                             TABLE PRODUK
                        ================================================== --}}
                        <div class="table-responsive">

                            <table
                                class="table table-bordered product-table table-sale">

                                <thead>

                                <tr>

                                    <th width="14%">
                                        Kode / No Seri
                                    </th>

                                    <th>
                                        Nama
                                    </th>

                                    <th width="17%">
                                        Harga Terendah
                                    </th>

                                    <th width="19%">
                                        Harga Jual
                                    </th>

                                    <th width="10%">
                                        Qty
                                    </th>

                                    <th width="8%">
                                        Aksi
                                    </th>

                                </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                 PEMBAYARAN
            ========================================================== --}}
            <div class="col-lg-4">

                <div class="payment-card">

                    <div class="payment-header">
                        Pembayaran
                    </div>

                    <div class="payment-body">

                        <form
                            action=""
                            class="form-pembelian"
                            method="post">

                            @csrf

                            <input
                                type="hidden"
                                name="divisi_id"
                                id="form_divisi_id">

                            <input
                                type="hidden"
                                name="total_item"
                                id="total_item">


                            {{-- =================================================
                                 PAYMENT GRID
                            ================================================== --}}
                            <div class="payment-grid">

                                {{-- SUBTOTAL --}}
                                <div class="payment-field">

                                    <label for="totalrp">
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        id="totalrp"
                                        class="form-control payment-total"
                                        value="Rp 0"
                                        readonly>

                                </div>


                                {{-- DISKON --}}
                                <div class="payment-field">

                                    <label for="diskon">
                                        Diskon
                                    </label>

                                    <input
                                        type="text"
                                        name="diskon"
                                        id="diskon"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- PPN --}}
                                <div class="payment-field">

                                    <label for="ppn">
                                        PPN
                                    </label>

                                    <input
                                        type="text"
                                        name="ppn"
                                        id="ppn"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- PPH --}}
                                <div class="payment-field">

                                    <label for="pph">
                                        PPH
                                    </label>

                                    <input
                                        type="text"
                                        name="pph"
                                        id="pph"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- ONGKIR --}}
                                <div class="payment-field">

                                    <label for="ongkir">
                                        Ongkir Konsumen
                                    </label>

                                    <input
                                        type="text"
                                        name="ongkir"
                                        id="ongkir"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- ADMIN FEE --}}
                                <div class="payment-field">

                                    <label for="admin_fee">
                                        Biaya Admin
                                    </label>

                                    <input
                                        type="text"
                                        name="admin_fee"
                                        id="admin_fee"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- GRAND TOTAL --}}
                                <div class="payment-field">

                                    <label for="bayarrp">
                                        Grand Total
                                    </label>

                                    <input
                                        type="text"
                                        id="bayarrp"
                                        class="form-control payment-grand-total"
                                        value="Rp 0"
                                        readonly>

                                </div>


                                {{-- NOMINAL IN --}}
                                <div class="payment-field">

                                    <label for="nominal_in">
                                        Nominal In
                                    </label>

                                    <input
                                        type="text"
                                        name="nominal_in"
                                        id="nominal_in"
                                        class="form-control"
                                        value="0">

                                </div>


                                {{-- KEMBALIAN --}}
                                <div
                                    class="payment-field"
                                    id="kembalianWrapper">

                                    <label for="kembalian">
                                        Kembalian
                                    </label>

                                    <input
                                        type="text"
                                        id="kembalian"
                                        class="form-control payment-grand-total"
                                        value="Rp 0"
                                        readonly>

                                </div>

                            </div>


                            {{-- =================================================
                                 PAY PLAN
                            ================================================== --}}
                            <div class="payment-field mt-3">

                                <label for="deadlines">
                                    Pay Plan
                                </label>

                                <input
                                    type="text"
                                    class="form-control datepicker"
                                    name="deadlines"
                                    id="deadlines"
                                    placeholder="Tanggal Pay Plan">

                            </div>


                            {{-- =================================================
                                 INVOICE MANUAL
                            ================================================== --}}
                            @if(auth()->check() && auth()->user()->divisi_id == 3)

                                <div class="payment-field mt-3">

                                    <label for="inv_manual">
                                        Invoice Manual
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="inv_manual"
                                        id="inv_manual">

                                </div>

                            @endif


                            {{-- =================================================
                                 METODE PEMBAYARAN
                            ================================================== --}}
                            <div class="payment-method-wrapper">

                                <div class="payment-method-title">
                                    Metode Pembayaran
                                </div>

                                <div class="payment-method-options">

                                    <label class="payment-method-option">

                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="cash"
                                            id="payment_cash"
                                            checked>

                                        <i class="bx bx-money"></i>

                                        <span>
                                            Cash
                                        </span>

                                    </label>


                                    <label class="payment-method-option">

                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="transfer"
                                            id="payment_transfer">

                                        <i class="bx bx-transfer"></i>

                                        <span>
                                            Transfer Bank
                                        </span>

                                    </label>

                                </div>


                                {{-- TRANSFER --}}
                                <div
                                    id="transferFields"
                                    class="transfer-fields"
                                    style="display:none;">

                                    <div class="transfer-grid">

                                        <div class="payment-field">

                                            <label for="bank">
                                                Nama Bank
                                            </label>

                                            <select
                                                name="bank_id"
                                                id="bank"
                                                class="form-control">

                                                <option value="">
                                                    -- Pilih Bank --
                                                </option>

                                                @foreach($bank as $data)

                                                    <option
                                                        value="{{ $data->id }}">

                                                        {{ $data->name }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="payment-field">

                                            <label for="penerima">
                                                Nama Penerima
                                            </label>

                                            <input
                                                type="text"
                                                name="penerima"
                                                id="penerima"
                                                class="form-control"
                                                placeholder="Nama penerima">

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                 BUTTON
                            ================================================== --}}
                            <div class="save-wrapper">

                                <button
                                    type="submit"
                                    id="btnSave"
                                    class="btn btn-primary w-100 btn-simpan"
                                    disabled>

                                    <i class="bx bx-check-circle"></i>

                                    <span class="btn-save-text">
                                        Pilih Divisi Terlebih Dahulu
                                    </span>

                                </button>


                                <button
                                    type="button"
                                    class="btn btn-secondary w-100 mt-2"
                                    onclick="window.history.back()">

                                    Batal

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('head')

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

@endpush


@push('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        $(document).ready(function () {

            /* JAM REALTIME WIB */
            function updateRealtimeClock() {

                let now = new Date();

                // WIB = UTC+7
                let wib = new Date(
                    now.toLocaleString('en-US', {
                        timeZone: 'Asia/Jakarta'
                    })
                );

                let hours = String(wib.getHours()).padStart(2, '0');
                let minutes = String(wib.getMinutes()).padStart(2, '0');
                let seconds = String(wib.getSeconds()).padStart(2, '0');

                $('#clockHour').text(hours);
                $('#clockMinute').text(minutes);
                $('#clockSecond').text(seconds);
            }

// Jalankan pertama kali
            updateRealtimeClock();

// Update setiap 1 detik
            setInterval(updateRealtimeClock, 1000);
            /* =========================================================
               DATA TRANSAKSI
            ========================================================= */

            let accessoriesData = [];

            let itemsData = [];

            let selectedDivisionId = null;

            let isSaving = false;
            let userChangedNominalIn = false;


            /* =========================================================
               CACHE OPTION AWAL

               PENTING:
               Semua option disimpan dahulu sebelum kita filter.
               Jadi ketika divisi berubah, kita bisa membangun ulang
               option sesuai divisi yang dipilih.
            ========================================================= */

            let customerOptions = [];

            $('#single-select-field option').each(function () {

                let option = $(this);

                if (!option.val()) {
                    return;
                }

                customerOptions.push({

                    id: option.val(),

                    name: option.text().trim(),

                    divisi_id:
                        String(
                            option.attr('data-divisi-id') || ''
                        )

                });

            });


            let invoiceOptions = [];

            $('#invoice option').each(function () {

                let option = $(this);

                if (!option.val()) {
                    return;
                }

                invoiceOptions.push({

                    value: option.val(),

                    text: option.text().trim(),

                    divisi_id:
                        String(
                            option.attr('data-divisi-id') || ''
                        )

                });

            });


            let accessoryOptions = [];

            $('#productSelect optgroup[label="ACCESSORIES"] option')
                .each(function () {

                    let option = $(this);

                    let stok =
                        parseInt(
                            option.attr('data-stok')
                        ) || 0;


                    accessoryOptions.push({

                        value:
                            option.val(),

                        id:
                            option.attr('data-id'),

                        divisi_id:
                            String(
                                option.attr('data-divisi-id') || ''
                            ),

                        code:
                            option.attr('data-code') || '',

                        name:
                            option.attr('data-name') || '',

                        price:
                            option.attr('data-price') || 0,

                        price_bottom:
                            option.attr('data-price-bottom') || 0,

                        stok:
                        stok

                    });

                });


            let itemOptions = [];

            $('#productSelect optgroup[label="ALAT / ITEM"] option')
                .each(function () {

                    let option = $(this);

                    itemOptions.push({

                        value:
                            option.val(),

                        id:
                            option.attr('data-id'),

                        divisi_id:
                            String(
                                option.attr('data-divisi-id') || ''
                            ),

                        no_seri:
                            option.attr('data-no-seri') || '',

                        name:
                            option.attr('data-name') || '',

                        price:
                            option.attr('data-price') || 0,

                        price_bottom:
                            option.attr('data-price-bottom') || 0,

                        itemcategory_id:
                            option.attr('data-itemcategory-id') || '',

                        capital_price:
                            option.attr('data-capital-price') || 0,

                        created_at:
                            option.attr('data-created-at') || ''

                    });

                });


            /* =========================================================
               SELECT2
            ========================================================= */

            $('#divisionSelect').select2({

                theme: 'bootstrap-5',

                placeholder:
                    '-- Pilih Divisi Terlebih Dahulu --',

                allowClear: true,

                width: '100%'

            });


            $('#single-select-field').select2({

                theme: 'bootstrap-5',

                placeholder:
                    '-- Pilih Customer --',

                allowClear: true,

                width: '100%'

            });


            $('#bank').select2({

                theme: 'bootstrap-5',

                placeholder:
                    '-- Pilih Bank --',

                allowClear: true,

                width: '100%'

            });


            $('#productSelect').select2({

                theme: 'bootstrap-5',

                placeholder:
                    '-- Pilih Produk --',

                allowClear: true,

                width: '100%'

            });


            $('#invoice').select2({

                theme: 'bootstrap-5',

                placeholder:
                    '-- Pilih Invoice --',

                allowClear: true,

                width: '100%'

            });


            /* =========================================================
               DATATABLE
            ========================================================= */

            let table = $('.table-sale').DataTable({

                processing: false,

                autoWidth: false,

                data: [],

                dom: 'rt',

                bSort: false,

                paging: false,

                searching: false,

                info: false,

                columns: [

                    {

                        data: 'code',

                        defaultContent: ''

                    },

                    {

                        data: 'name',

                        defaultContent: ''

                    },

                    {

                        data: 'price_bottom',

                        render: function (data) {

                            return formatRupiah(
                                data || 0
                            );

                        }

                    },

                    {

                        data: 'price',

                        render: function () {

                            return `

                                <input
                                    type="text"
                                    class="form-control price-input"
                                    value="0"
                                    autocomplete="off">

                            `;

                        }

                    },

                    {

                        data: 'stok',

                        render: function (
                            data,
                            type,
                            row
                        ) {

                            let qty =
                                parseInt(data) || 1;


                            if (
                                row.type === 'item'
                            ) {

                                return `

                                    <input
                                        type="number"
                                        class="form-control stok-input"
                                        value="1"
                                        min="1"
                                        max="1"
                                        readonly>

                                `;

                            }


                            return `

                                <input
                                    type="number"
                                    class="form-control stok-input"
                                    value="${qty}"
                                    min="1"
                                    max="${qty}">

                            `;

                        }

                    },

                    {

                        data: null,

                        searchable: false,

                        sortable: false,

                        render: function () {

                            return `

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-delete-row">

                                    <i class="bx bx-trash"></i>

                                </button>

                            `;

                        }

                    }

                ]

            });


            /* =========================================================
               FORMAT NUMBER
            ========================================================= */

            function formatNumber(value) {

                value =
                    parseRupiah(value);

                return value.toLocaleString(
                    'id-ID'
                );

            }


            /* =========================================================
               FORMAT RUPIAH
            ========================================================= */

            window.formatRupiah =
                function (elementOrValue) {

                    let value;


                    if (
                        typeof elementOrValue === 'object' &&
                        elementOrValue !== null
                    ) {

                        value =
                            elementOrValue.value;

                    } else {

                        value =
                            elementOrValue;

                    }


                    value =
                        String(value ?? '')
                            .replace(
                                /[^0-9]/g,
                                ''
                            );


                    if (!value) {

                        value = '0';

                    }


                    let number =
                        parseInt(value) || 0;


                    let formatted =
                        number.toLocaleString(
                            'id-ID'
                        );


                    if (
                        typeof elementOrValue === 'object' &&
                        elementOrValue !== null
                    ) {

                        elementOrValue.value =
                            formatted;

                    }


                    return 'Rp ' + formatted;

                };


            /* =========================================================
               PARSE RUPIAH
            ========================================================= */

            function parseRupiah(value) {

                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {

                    return 0;

                }


                return parseInt(
                    String(value)
                        .replace(
                            /[^0-9]/g,
                            ''
                        )
                ) || 0;

            }


            /* =========================================================
               REBUILD CUSTOMER BERDASARKAN DIVISI

               TIDAK MENGGUNAKAN CONTROLLER
               TIDAK MENGGUNAKAN AJAX
               MURNI FILTER DI BLADE / JAVASCRIPT
            ========================================================= */

            function rebuildCustomerByDivision(divisionId) {

                let select = $('#single-select-field');

                /*
                 * Hapus option lama
                 */
                select.empty();

                /*
                 * Placeholder
                 */
                select.append(`
        <option value="">
            -- Pilih Customer --
        </option>
    `);

                /*
                 * Jika belum ada divisi,
                 * jangan tampilkan customer.
                 */
                if (!divisionId) {

                    select
                        .val(null)
                        .trigger('change');

                    return;
                }

                /*
                 * Filter customer berdasarkan divisi
                 */
                customerOptions.forEach(function (customer) {

                    if (
                        String(customer.divisi_id) ===
                        String(divisionId)
                    ) {

                        select.append(`

                <option
                    value="${escapeAttribute(customer.id)}"
                    data-divisi-id="${escapeAttribute(customer.divisi_id)}">

                    ${escapeHtml(customer.name)}

                </option>

            `);

                    }

                });

                /*
                 * Reset selected customer
                 */
                select
                    .val(null)
                    .trigger('change');

            }


            /* =========================================================
               REBUILD INVOICE BERDASARKAN DIVISI
            ========================================================= */


            /* =========================================================
               REBUILD PRODUCT BERDASARKAN DIVISI

               ACCESSORIES:
               - divisi sama
               - stok >= 1

               ITEM:
               - divisi sama
            ========================================================= */

            function rebuildProductByDivision(
                divisionId
            ) {

                let select =
                    $('#productSelect');


                /*
                 * Hapus semua option lama
                 */

                select.empty();


                /*
                 * Placeholder
                 */

                select.append(`

                    <option value="">
                        -- Pilih Produk --
                    </option>

                `);


                /*
                 * Belum memilih divisi
                 */

                if (!divisionId) {

                    select
                        .val(null)
                        .trigger('change');

                    return;

                }


                /* =====================================================
                   ACCESSORIES
                ====================================================== */

                let filteredAccessories =
                    accessoryOptions.filter(
                        function (accessory) {

                            return (

                                String(
                                    accessory.divisi_id
                                ) ===
                                String(
                                    divisionId
                                )

                                &&

                                parseInt(
                                    accessory.stok
                                ) >= 1

                            );

                        }
                    );


                /*
                 * Hanya buat optgroup kalau ada data
                 */

                if (
                    filteredAccessories.length > 0
                ) {

                    let accessoryGroup =
                        $('<optgroup>')
                            .attr(
                                'label',
                                'ACCESSORIES'
                            );


                    filteredAccessories.forEach(
                        function (accessory) {

                            accessoryGroup.append(`

                                <option
                                    value="${escapeAttribute(accessory.value)}"
                                    data-type="accessory"
                                    data-id="${escapeAttribute(accessory.id)}"
                                    data-divisi-id="${escapeAttribute(accessory.divisi_id)}"
                                    data-code="${escapeAttribute(accessory.code)}"
                                    data-name="${escapeAttribute(accessory.name)}"
                                    data-price="${escapeAttribute(accessory.price)}"
                                    data-price-bottom="${escapeAttribute(accessory.price_bottom)}"
                                    data-stok="${escapeAttribute(accessory.stok)}">

                                    ${escapeHtml(
                                accessory.code
                            )}
                                    -
                                    ${escapeHtml(
                                accessory.name
                            )}
                                    -
                                    Stok:
                                    ${escapeHtml(
                                accessory.stok
                            )}

                                </option>

                            `);

                        }
                    );


                    select.append(
                        accessoryGroup
                    );

                }


                /* =====================================================
                   ITEM / ALAT
                ====================================================== */

                let filteredItems =
                    itemOptions.filter(
                        function (item) {

                            return (
                                String(
                                    item.divisi_id
                                ) ===
                                String(
                                    divisionId
                                )
                            );

                        }
                    );


                if (
                    filteredItems.length > 0
                ) {

                    let itemGroup =
                        $('<optgroup>')
                            .attr(
                                'label',
                                'ALAT / ITEM'
                            );


                    filteredItems.forEach(
                        function (item) {

                            itemGroup.append(`

                                <option
                                    value="${escapeAttribute(item.value)}"
                                    data-type="item"
                                    data-id="${escapeAttribute(item.id)}"
                                    data-divisi-id="${escapeAttribute(item.divisi_id)}"
                                    data-no-seri="${escapeAttribute(item.no_seri)}"
                                    data-name="${escapeAttribute(item.name)}"
                                    data-price="${escapeAttribute(item.price)}"
                                    data-price-bottom="${escapeAttribute(item.price_bottom)}"
                                    data-itemcategory-id="${escapeAttribute(item.itemcategory_id)}"
                                    data-capital-price="${escapeAttribute(item.capital_price)}"
                                    data-created-at="${escapeAttribute(item.created_at)}">

                                    ${escapeHtml(
                                item.no_seri
                            )}
                                    -
                                    ${escapeHtml(
                                item.name
                            )}

                                </option>

                            `);

                        }
                    );


                    select.append(
                        itemGroup
                    );

                }


                /*
                 * Reset pilihan
                 */

                select
                    .val(null)
                    .trigger('change');

            }


            /* =========================================================
               FILTER SEMUA DATA BERDASARKAN DIVISI
            ========================================================= */

            function filterByDivision() {

                let divisionId =
                    selectedDivisionId;


                /*
                 * CUSTOMER
                 */

                rebuildCustomerByDivision(
                    divisionId
                );


                /*
                 * INVOICE
                 */


                /*
                 * PRODUCT
                 */

                rebuildProductByDivision(
                    divisionId
                );

            }


            /* =========================================================
               ENABLE / DISABLE FORM BERDASARKAN DIVISI
            ========================================================= */

            function updateDivisionState() {

                let divisionId = $('#divisionSelect').val();

                selectedDivisionId = divisionId
                    ? String(divisionId)
                    : null;


                $('#form_divisi_id').val(
                    selectedDivisionId || ''
                );


                /*
                 * =====================================================
                 * DIVISI BELUM DIPILIH
                 * =====================================================
                 */

                if (!selectedDivisionId) {

                    $('#divisionWarning').show();

                    $('#single-select-field')
                        .prop('disabled', true);

                    $('#invoice')
                        .prop('disabled', true);

                    $('#productSelect')
                        .prop('disabled', true);

                    $('#code')
                        .prop('disabled', true)
                        .attr(
                            'placeholder',
                            'Pilih divisi terlebih dahulu...'
                        );

                    $('#btnSearchCode')
                        .prop('disabled', true);

                    $('#btnSave')
                        .prop('disabled', true)
                        .html(`
                <i class="bx bx-check-circle"></i>
                <span class="btn-save-text">
                    Pilih Divisi Terlebih Dahulu
                </span>
            `);

                    return;
                }


                /*
                 * =====================================================
                 * DIVISI SUDAH DIPILIH
                 * =====================================================
                 */

                $('#divisionWarning').hide();

                $('#single-select-field')
                    .prop('disabled', false);

                $('#invoice')
                    .prop('disabled', false);

                $('#productSelect')
                    .prop('disabled', false);

                $('#code')
                    .prop('disabled', false)
                    .attr(
                        'placeholder',
                        'Scan barcode produk...'
                    );

                $('#btnSearchCode')
                    .prop('disabled', false);

                $('#btnSave')
                    .prop('disabled', false)
                    .html(`
            <i class="bx bx-check-circle"></i>
            <span class="btn-save-text">
                Simpan Transaksi
            </span>
        `);

                filterByDivision();
            }


            /* =========================================================
               CHANGE DIVISI
            ========================================================= */

            $('#divisionSelect').on(
                'change',
                function () {

                    let newDivisionId =
                        $(this).val();


                    /*
                     * Jika sebelumnya sudah ada produk,
                     * tanyakan terlebih dahulu.
                     */

                    if (

                        selectedDivisionId &&

                        String(
                            selectedDivisionId
                        ) !==
                        String(
                            newDivisionId
                        ) &&

                        (
                            accessoriesData.length > 0 ||
                            itemsData.length > 0
                        )

                    ) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Divisi Diubah',

                            text:
                                'Produk yang sudah dipilih akan dihapus karena divisi transaksi berubah.',

                            showCancelButton:
                                true,

                            confirmButtonText:
                                'Ya, Ubah Divisi',

                            cancelButtonText:
                                'Batal'

                        }).then(
                            function (result) {

                                if (
                                    result.isConfirmed
                                ) {

                                    /*
                                     * Hapus transaksi
                                     */

                                    accessoriesData =
                                        [];

                                    itemsData =
                                        [];


                                    renderTable();


                                    /*
                                     * Set divisi baru
                                     */

                                    selectedDivisionId =
                                        newDivisionId
                                            ? String(
                                                newDivisionId
                                            )
                                            : null;


                                    updateDivisionState();


                                    /*
                                     * Focus barcode
                                     */

                                    $('#code')
                                        .focus();

                                } else {

                                    /*
                                     * Kembalikan divisi
                                     */

                                    $('#divisionSelect')
                                        .val(
                                            selectedDivisionId
                                        )
                                        .trigger(
                                            'change.select2'
                                        );

                                }

                            }
                        );


                        return;

                    }


                    selectedDivisionId =
                        newDivisionId
                            ? String(
                                newDivisionId
                            )
                            : null;


                    updateDivisionState();

                }
            );


            /* =========================================================
               RENDER TABLE
            ========================================================= */

            function renderTable() {

                table.clear();


                /* =====================================================
                   ACCESSORIES
                ====================================================== */

                accessoriesData.forEach(
                    function (accessory) {

                        table.row.add({

                            type:
                                'accessory',

                            accessories_id:
                            accessory.accessories_id,

                            code:
                            accessory.code_acces,

                            code_acces:
                            accessory.code_acces,

                            name:
                            accessory.name,

                            price_bottom:
                                parseFloat(
                                    accessory.price_bottom || 0
                                ),

                            price:
                                parseFloat(
                                    accessory.price_sale || 0
                                ),

                            stok:
                                parseInt(
                                    accessory.qty || 1
                                )

                        });

                    }
                );


                /* =====================================================
                   ITEMS
                ====================================================== */

                itemsData.forEach(
                    function (item) {

                        table.row.add({

                            type:
                                'item',

                            id:
                            item.id,

                            code:
                            item.no_seri,

                            no_seri:
                            item.no_seri,

                            name:
                            item.name,

                            price_bottom:
                                parseFloat(
                                    item.price_bottom || 0
                                ),

                            price:
                                parseFloat(
                                    item.price || 0
                                ),

                            stok:
                                1,

                            itemcategory_id:
                            item.itemcategory_id,

                            capital_price:
                            item.capital_price,

                            created_at:
                            item.created_at

                        });

                    }
                );


                table.draw();


                calculateTotal();

            }


            /* =========================================================
               HITUNG TOTAL
            ========================================================= */

            function calculateTotal() {

                let total = 0;

                let totalQty = 0;


                table.rows().every(
                    function () {

                        let row =
                            $(this.node());


                        let price =
                            parseRupiah(
                                row.find(
                                    '.price-input'
                                ).val()
                            );


                        let qty =
                            parseInt(
                                row.find(
                                    '.stok-input'
                                ).val()
                            ) || 0;


                        if (
                            price > 0 &&
                            qty > 0
                        ) {

                            total +=
                                price * qty;


                            totalQty +=
                                qty;

                        }

                    }
                );


                let diskon =
                    parseRupiah(
                        $('#diskon').val()
                    );


                let ppn =
                    parseRupiah(
                        $('#ppn').val()
                    );


                let pph =
                    parseRupiah(
                        $('#pph').val()
                    );


                let ongkir =
                    parseRupiah(
                        $('#ongkir').val()
                    );


                let adminFee =
                    parseRupiah(
                        $('#admin_fee').val()
                    );


                let bayar =
                    total;


                bayar -= diskon;

                bayar += ppn;

                bayar -= pph;

                bayar += ongkir;

                bayar -= adminFee;


                if (bayar < 0) {

                    bayar = 0;

                }


                $('#totalrp').val(
                    'Rp ' +
                    total.toLocaleString(
                        'id-ID'
                    )
                );


                $('#bayarrp').val(
                    'Rp ' +
                    bayar.toLocaleString(
                        'id-ID'
                    )
                );


                $('#total_item').val(
                    totalQty
                );


                /* =========================================================
   NOMINAL IN
========================================================= */

                let nominalIn = parseRupiah(
                    $('#nominal_in').val()
                );


                /*
                 * Jika user belum mengubah Nominal In secara manual,
                 * maka Nominal In selalu mengikuti Grand Total.
                 */
                if (!userChangedNominalIn) {

                    nominalIn = bayar;

                    $('#nominal_in').val(
                        bayar.toLocaleString('id-ID')
                    );

                }


                let method =
                    $('input[name="payment_method"]:checked')
                        .val();


                if (
                    method === 'cash'
                ) {

                    let kembalian =
                        nominalIn - bayar;


                    if (
                        kembalian < 0
                    ) {

                        kembalian = 0;

                    }


                    $('#kembalian').val(
                        'Rp ' +
                        kembalian.toLocaleString(
                            'id-ID'
                        )
                    );

                } else {

                    $('#kembalian').val(
                        'Rp 0'
                    );

                }

            }


            /* =========================================================
               INPUT HARGA / QTY
            ========================================================= */

            $(document).on(
                'input',
                '.price-input, .stok-input',
                function () {

                    let input =
                        $(this);


                    if (
                        input.hasClass(
                            'price-input'
                        )
                    ) {

                        let value =
                            parseRupiah(
                                input.val()
                            );


                        input.val(
                            value.toLocaleString(
                                'id-ID'
                            )
                        );

                    }


                    if (
                        input.hasClass(
                            'stok-input'
                        )
                    ) {

                        let qty =
                            parseInt(
                                input.val()
                            ) || 1;


                        if (
                            qty < 1
                        ) {

                            qty = 1;

                        }


                        /*
                         * Ambil stok maksimum
                         */

                        let max =
                            parseInt(
                                input.attr('max')
                            );


                        if (
                            max &&
                            qty > max
                        ) {

                            qty = max;

                        }


                        input.val(qty);

                    }


                    calculateTotal();

                }
            );


            /* =========================================================
               INPUT PAYMENT
            ========================================================= */

            $('#diskon, #ppn, #pph, #ongkir, #admin_fee')
                .on(
                    'input',
                    function () {

                        formatRupiah(this);

                        calculateTotal();

                    }
                );


            /* =========================================================
               NOMINAL IN
            ========================================================= */

            $('#nominal_in').on('input', function () {

                userChangedNominalIn = true;

                formatRupiah(this);

                calculateTotal();

            });


            /* =========================================================
               ADD ACCESSORY
            ========================================================= */

            function addAccessory(
                accessory
            ) {

                /*
                 * VALIDASI DIVISI
                 */

                if (!selectedDivisionId) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Divisi Belum Dipilih',

                        text:
                            'Silakan pilih divisi terlebih dahulu.'

                    });

                    return;

                }


                /*
                 * VALIDASI DIVISI ACCESSORY
                 */

                if (

                    accessory.divisi_id &&

                    String(
                        accessory.divisi_id
                    ) !==
                    String(
                        selectedDivisionId
                    )

                ) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Divisi Tidak Sesuai',

                        text:
                            'Accessories tersebut bukan milik divisi yang dipilih.'

                    });

                    return;

                }


                /*
                 * VALIDASI STOK
                 *
                 * Accessories yang stok 0 atau minus
                 * tidak boleh masuk transaksi.
                 */

                let accessoryStock =
                    parseInt(
                        accessory.stok ??
                        accessory.stock ??
                        0
                    );


                if (
                    accessoryStock < 1
                ) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Stok Tidak Tersedia',

                        text:
                            'Accessories tersebut tidak memiliki stok yang tersedia.'

                    });

                    return;

                }


                let accessoryId =
                    accessory.accessories_id ??
                    accessory.id;


                if (!accessoryId) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Data Accessories Tidak Valid',

                        text:
                            'ID accessories tidak ditemukan.'

                    });

                    return;

                }


                let existingIndex =
                    accessoriesData.findIndex(
                        function (item) {

                            let existingId =
                                item.accessories_id ??
                                item.id;


                            return (
                                String(
                                    existingId
                                ) ===
                                String(
                                    accessoryId
                                )
                            );

                        }
                    );


                if (
                    existingIndex !== -1
                ) {

                    let existing =
                        accessoriesData[
                            existingIndex
                            ];


                    let currentQty =
                        parseInt(
                            existing.qty
                        ) || 0;


                    /*
                     * Jangan melebihi stok
                     */

                    if (
                        currentQty >=
                        accessoryStock
                    ) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Stok Tidak Mencukupi',

                            text:
                                'Qty tidak dapat melebihi stok yang tersedia. Stok: ' +
                                accessoryStock

                        });

                        return;

                    }


                    existing.qty =
                        currentQty + 1;


                    existing.subtotal =
                        existing.qty *
                        parseFloat(
                            existing.price_sale || 0
                        );


                    renderTable();

                    return;

                }


                let priceSale =
                    parseFloat(
                        accessory.price_sale ??
                        accessory.price ??
                        0
                    );


                let priceBottom =
                    parseFloat(
                        accessory.price_bottom ??
                        0
                    );


                accessoriesData.push({

                    accessories_id:
                    accessoryId,

                    divisi_id:
                    selectedDivisionId,

                    code_acces:
                        accessory.code_acces ??
                        accessory.code ??
                        '',

                    name:
                        accessory.name ??
                        '',

                    price_bottom:
                    priceBottom,

                    price_sale:
                    priceSale,

                    qty:
                        1,

                    stok:
                    accessoryStock,

                    subtotal:
                    priceSale

                });


                renderTable();

            }


            /* =========================================================
               ADD ITEM
            ========================================================= */

            function addItem(
                item
            ) {

                /*
                 * VALIDASI DIVISI
                 */

                if (!selectedDivisionId) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Divisi Belum Dipilih',

                        text:
                            'Silakan pilih divisi terlebih dahulu.'

                    });

                    return;

                }


                /*
                 * VALIDASI DIVISI ITEM
                 */

                if (

                    item.divisi_id &&

                    String(
                        item.divisi_id
                    ) !==
                    String(
                        selectedDivisionId
                    )

                ) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Divisi Tidak Sesuai',

                        text:
                            'Item tersebut bukan milik divisi yang dipilih.'

                    });

                    return;

                }


                let noSeri =
                    item.no_seri ??
                    item.serial_number ??
                    item.code;


                if (!noSeri) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Data Item Tidak Valid',

                        text:
                            'No seri item tidak ditemukan.'

                    });

                    return;

                }


                let existing =
                    itemsData.find(
                        function (data) {

                            return (

                                String(
                                    data.no_seri
                                ) ===
                                String(
                                    noSeri
                                )

                            );

                        }
                    );


                if (existing) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Item Sudah Ditambahkan',

                        html:
                            'No Seri <strong>' +
                            escapeHtml(
                                noSeri
                            ) +
                            '</strong> sudah ada di transaksi.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                itemsData.push({

                    id:
                    item.id,

                    divisi_id:
                    selectedDivisionId,

                    itemcategory_id:
                    item.itemcategory_id,

                    name:
                    item.name,

                    no_seri:
                    noSeri,

                    price:
                        parseFloat(
                            item.price || 0
                        ),

                    price_bottom:
                        parseFloat(
                            item.price_bottom || 0
                        ),

                    capital_price:
                        parseFloat(
                            item.capital_price || 0
                        ),

                    created_at:
                    item.created_at

                });


                renderTable();

            }


            /* =========================================================
               PROCESS CODE / SCAN BARCODE
            ========================================================= */

            function processCode() {

                /*
                 * WAJIB DIVISI
                 */

                if (!selectedDivisionId) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Divisi Belum Dipilih',

                        text:
                            'Silakan pilih divisi terlebih dahulu sebelum scan barang.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                let codeSale =
                    $('#code')
                        .val()
                        .trim();


                if (!codeSale) {

                    return;

                }


                $.ajax({

                    url:
                        '{{ route('manager.sale.checkcode') }}',

                    method:
                        'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        code:
                        codeSale,

                        divisi_id:
                        selectedDivisionId

                    },


                    beforeSend:
                        function () {

                            $('#code')
                                .prop(
                                    'disabled',
                                    true
                                );


                            $('#btnSearchCode')
                                .prop(
                                    'disabled',
                                    true
                                );

                        },


                    success:
                        function (response) {

                            console.log(
                                'Response:',
                                response
                            );


                            if (

                                !response ||

                                response.status !==
                                'success'

                            ) {

                                Swal.fire({

                                    icon:
                                        'error',

                                    title:
                                        'Produk Tidak Ditemukan',

                                    text:
                                        response?.message ||
                                        'Data produk tidak ditemukan.',

                                    confirmButtonText:
                                        'OK'

                                });

                                return;

                            }


                            /*
                             * VALIDASI DIVISI RESPONSE
                             */

                            if (

                                response.data &&

                                response.data.divisi_id &&

                                String(
                                    response.data.divisi_id
                                ) !==
                                String(
                                    selectedDivisionId
                                )

                            ) {

                                Swal.fire({

                                    icon:
                                        'error',

                                    title:
                                        'Divisi Tidak Sesuai',

                                    text:
                                        'Barang tersebut bukan milik divisi yang dipilih.',

                                    confirmButtonText:
                                        'OK'

                                });

                                return;

                            }


                            /*
                             * ACCESSORY
                             */

                            if (
                                response.type ===
                                'accessory'
                            ) {

                                /*
                                 * Validasi stok dari response
                                 */

                                let stock =
                                    parseInt(
                                        response.data.stok ??
                                        response.data.stock ??
                                        0
                                    );


                                if (
                                    stock < 1
                                ) {

                                    Swal.fire({

                                        icon:
                                            'warning',

                                        title:
                                            'Stok Tidak Tersedia',

                                        text:
                                            'Accessories tersebut stoknya sudah habis.'

                                    });

                                    return;

                                }


                                addAccessory(
                                    response.data
                                );

                                return;

                            }


                            /*
                             * ITEM
                             */

                            if (
                                response.type ===
                                'item'
                            ) {

                                addItem(
                                    response.data
                                );

                                return;

                            }


                            Swal.fire({

                                icon:
                                    'warning',

                                title:
                                    'Jenis Produk Tidak Dikenali',

                                text:
                                    'Jenis produk tidak dikenali.',

                                confirmButtonText:
                                    'OK'

                            });

                        },


                    error:
                        function (xhr) {

                            console.error(
                                'Check Code Error:',
                                xhr.responseText
                            );


                            let message =
                                'Terjadi kesalahan saat mengecek kode.';


                            if (

                                xhr.responseJSON &&

                                xhr.responseJSON.message

                            ) {

                                message =
                                    xhr.responseJSON.message;

                            }


                            Swal.fire({

                                icon:
                                    'error',

                                title:
                                    'Gagal',

                                text:
                                message,

                                confirmButtonText:
                                    'OK'

                            });

                        },


                    complete:
                        function () {

                            $('#code')
                                .prop(
                                    'disabled',
                                    !selectedDivisionId
                                )
                                .val('')
                                .focus();


                            $('#btnSearchCode')
                                .prop(
                                    'disabled',
                                    !selectedDivisionId
                                );

                        }

                });

            }


            /* =========================================================
               ENTER CODE
            ========================================================= */

            $('#code').on(
                'keydown',
                function (e) {

                    if (
                        e.key === 'Enter'
                    ) {

                        e.preventDefault();

                        processCode();

                    }

                }
            );


            /* =========================================================
               BUTTON CARI
            ========================================================= */

            $('#btnSearchCode').on(
                'click',
                function () {

                    processCode();

                }
            );


            /* =========================================================
               SELECT PRODUCT
            ========================================================= */

            $('#productSelect').on(
                'change',
                function () {

                    if (!selectedDivisionId) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Divisi Belum Dipilih',

                            text:
                                'Silakan pilih divisi terlebih dahulu.'

                        });


                        $(this)
                            .val(null)
                            .trigger('change');


                        return;

                    }


                    let option =
                        $(this)
                            .find(
                                'option:selected'
                            );


                    if (!option.val()) {

                        return;

                    }


                    let type =
                        option.attr(
                            'data-type'
                        );


                    let optionDivisionId =
                        option.attr(
                            'data-divisi-id'
                        );


                    /*
                     * VALIDASI DIVISI
                     */

                    if (

                        String(
                            optionDivisionId
                        ) !==
                        String(
                            selectedDivisionId
                        )

                    ) {

                        Swal.fire({

                            icon:
                                'error',

                            title:
                                'Divisi Tidak Sesuai',

                            text:
                                'Produk tersebut bukan milik divisi yang dipilih.'

                        });


                        $(this)
                            .val(null)
                            .trigger('change');


                        return;

                    }


                    /* =================================================
                       ACCESSORY
                    ================================================== */

                    if (
                        type ===
                        'accessory'
                    ) {

                        let stock =
                            parseInt(
                                option.attr(
                                    'data-stok'
                                )
                            ) || 0;


                        /*
                         * Jangan tampilkan / proses stok 0
                         */

                        if (
                            stock < 1
                        ) {

                            Swal.fire({

                                icon:
                                    'warning',

                                title:
                                    'Stok Tidak Tersedia',

                                text:
                                    'Accessories tersebut tidak memiliki stok.'

                            });


                            $(this)
                                .val(null)
                                .trigger(
                                    'change'
                                );


                            return;

                        }


                        addAccessory({

                            id:
                                option.attr(
                                    'data-id'
                                ),

                            accessories_id:
                                option.attr(
                                    'data-id'
                                ),

                            divisi_id:
                                option.attr(
                                    'data-divisi-id'
                                ),

                            code_acces:
                                option.attr(
                                    'data-code'
                                ),

                            name:
                                option.attr(
                                    'data-name'
                                ),

                            price:
                                option.attr(
                                    'data-price'
                                ),

                            price_bottom:
                                option.attr(
                                    'data-price-bottom'
                                ),

                            price_sale:
                                option.attr(
                                    'data-price'
                                ),

                            stok:
                            stock

                        });

                    }


                    /* =================================================
                       ITEM
                    ================================================== */

                    else if (
                        type ===
                        'item'
                    ) {

                        addItem({

                            id:
                                option.attr(
                                    'data-id'
                                ),

                            divisi_id:
                                option.attr(
                                    'data-divisi-id'
                                ),

                            itemcategory_id:
                                option.attr(
                                    'data-itemcategory-id'
                                ),

                            name:
                                option.attr(
                                    'data-name'
                                ),

                            no_seri:
                                option.attr(
                                    'data-no-seri'
                                ),

                            price:
                                option.attr(
                                    'data-price'
                                ),

                            price_bottom:
                                option.attr(
                                    'data-price-bottom'
                                ),

                            capital_price:
                                option.attr(
                                    'data-capital-price'
                                ),

                            created_at:
                                option.attr(
                                    'data-created-at'
                                )

                        });

                    }


                    /*
                     * Reset select
                     */

                    $('#productSelect')
                        .val(null)
                        .trigger(
                            'change.select2'
                        );

                }
            );


            /* =========================================================
               DELETE ROW
            ========================================================= */

            $(document).on(
                'click',
                '.btn-delete-row',
                function () {

                    let row =
                        $(this)
                            .closest('tr');


                    let rowData =
                        table
                            .row(row)
                            .data();


                    if (!rowData) {

                        return;

                    }


                    if (
                        rowData.type ===
                        'accessory'
                    ) {

                        accessoriesData =
                            accessoriesData.filter(
                                function (item) {

                                    return (

                                        String(
                                            item.accessories_id
                                        ) !==
                                        String(
                                            rowData.accessories_id
                                        )

                                    );

                                }
                            );

                    } else if (
                        rowData.type ===
                        'item'
                    ) {

                        itemsData =
                            itemsData.filter(
                                function (item) {

                                    return (

                                        String(
                                            item.no_seri
                                        ) !==
                                        String(
                                            rowData.no_seri
                                        )

                                    );

                                }
                            );

                    }


                    renderTable();

                }
            );


            /* =========================================================
               SYNC TABLE DATA
            ========================================================= */

            function syncTableData() {

                /* =====================================================
                   ACCESSORIES
                ====================================================== */

                accessoriesData.forEach(
                    function (accessory) {

                        table.rows().every(
                            function () {

                                let data =
                                    this.data();


                                if (

                                    data.type ===
                                    'accessory' &&

                                    String(
                                        data.accessories_id
                                    ) ===
                                    String(
                                        accessory.accessories_id
                                    )

                                ) {

                                    let row =
                                        $(this.node());


                                    let qty =
                                        parseInt(
                                            row.find(
                                                '.stok-input'
                                            ).val()
                                        ) || 1;


                                    let price =
                                        parseRupiah(
                                            row.find(
                                                '.price-input'
                                            ).val()
                                        );


                                    accessory.qty =
                                        qty;


                                    accessory.price_sale =
                                        price;


                                    accessory.subtotal =
                                        qty *
                                        price;

                                }

                            }
                        );

                    }
                );


                /* =====================================================
                   ITEMS
                ====================================================== */

                itemsData.forEach(
                    function (item) {

                        table.rows().every(
                            function () {

                                let data =
                                    this.data();


                                if (

                                    data.type ===
                                    'item' &&

                                    String(
                                        data.no_seri
                                    ) ===
                                    String(
                                        item.no_seri
                                    )

                                ) {

                                    let row =
                                        $(this.node());


                                    item.price =
                                        parseRupiah(
                                            row.find(
                                                '.price-input'
                                            ).val()
                                        );

                                }

                            }
                        );

                    }
                );

            }


            /* =========================================================
               VALIDASI HARGA MINIMUM
            ========================================================= */

            function validateMinimumPrice() {

                syncTableData();


                /* =====================================================
                   ACCESSORY
                ====================================================== */

                for (
                    let i = 0;
                    i < accessoriesData.length;
                    i++
                ) {

                    let accessory =
                        accessoriesData[i];


                    let price =
                        parseFloat(
                            accessory.price_sale || 0
                        );


                    let minimum =
                        parseFloat(
                            accessory.price_bottom || 0
                        );


                    if (
                        price < minimum
                    ) {

                        return {

                            valid:
                                false,

                            type:
                                'accessory',

                            name:
                            accessory.name,

                            price:
                            price,

                            minimum:
                            minimum

                        };

                    }

                }


                /* =====================================================
                   ITEM
                ====================================================== */

                for (
                    let i = 0;
                    i < itemsData.length;
                    i++
                ) {

                    let item =
                        itemsData[i];


                    let price =
                        parseFloat(
                            item.price || 0
                        );


                    let minimum =
                        parseFloat(
                            item.price_bottom || 0
                        );


                    if (
                        price < minimum
                    ) {

                        return {

                            valid:
                                false,

                            type:
                                'item',

                            name:
                            item.name,

                            no_seri:
                            item.no_seri,

                            price:
                            price,

                            minimum:
                            minimum

                        };

                    }

                }


                return {

                    valid:
                        true

                };

            }


            /* =========================================================
               PAYMENT METHOD
            ========================================================= */

            function updatePaymentMethod() {

                let method =
                    $('input[name="payment_method"]:checked')
                        .val();


                if (
                    method ===
                    'cash'
                ) {

                    $('#transferFields')
                        .stop(
                            true,
                            true
                        )
                        .slideUp(150);


                    $('#kembalianWrapper')
                        .stop(
                            true,
                            true
                        )
                        .slideDown(150);


                    $('#bank')
                        .val(null)
                        .trigger('change');


                    $('#penerima')
                        .val('');

                } else if (
                    method ===
                    'transfer'
                ) {

                    $('#transferFields')
                        .stop(
                            true,
                            true
                        )
                        .slideDown(150);


                    $('#kembalianWrapper')
                        .stop(
                            true,
                            true
                        )
                        .slideUp(150);

                }


                calculateTotal();

            }


            $('input[name="payment_method"]')
                .on(
                    'change',
                    function () {

                        updatePaymentMethod();

                    }
                );


            /* =========================================================
               SAVE TRANSACTION
            ========================================================= */

            function saveTransaction() {

                /* =====================================================
                   VALIDASI DIVISI
                ====================================================== */

                if (!selectedDivisionId) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Divisi Belum Dipilih',

                        text:
                            'Silakan pilih divisi terlebih dahulu sebelum menyimpan transaksi.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                if (isSaving) {

                    return;

                }


                /* =====================================================
                   VALIDASI CUSTOMER
                ====================================================== */

                let customerId =
                    $('#single-select-field')
                        .val();


                if (!customerId) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Customer Belum Dipilih',

                        text:
                            'Silakan pilih customer terlebih dahulu.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   VALIDASI PRODUK
                ====================================================== */

                if (

                    accessoriesData.length === 0 &&

                    itemsData.length === 0

                ) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Data Kosong',

                        text:
                            'Silakan masukkan alat atau accessories terlebih dahulu.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   VALIDASI CUSTOMER DIVISI
                ====================================================== */

                let selectedCustomer =
                    $('#single-select-field option:selected');


                let customerDivisionId =
                    selectedCustomer.attr(
                        'data-divisi-id'
                    );


                if (

                    String(
                        customerDivisionId
                    ) !==
                    String(
                        selectedDivisionId
                    )

                ) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Customer Tidak Sesuai Divisi',

                        text:
                            'Customer yang dipilih bukan milik divisi transaksi.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   VALIDASI PRODUK DIVISI
                ====================================================== */

                let invalidDivisionProduct =
                    false;


                accessoriesData.forEach(
                    function (accessory) {

                        if (

                            accessory.divisi_id &&

                            String(
                                accessory.divisi_id
                            ) !==
                            String(
                                selectedDivisionId
                            )

                        ) {

                            invalidDivisionProduct =
                                true;

                        }

                    }
                );


                itemsData.forEach(
                    function (item) {

                        if (

                            item.divisi_id &&

                            String(
                                item.divisi_id
                            ) !==
                            String(
                                selectedDivisionId
                            )

                        ) {

                            invalidDivisionProduct =
                                true;

                        }

                    }
                );


                if (
                    invalidDivisionProduct
                ) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Produk Tidak Sesuai Divisi',

                        text:
                            'Terdapat produk yang bukan milik divisi yang dipilih.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   VALIDASI STOK ACCESSORIES
                ====================================================== */

                let invalidStock =
                    false;


                accessoriesData.forEach(
                    function (accessory) {

                        let qty =
                            parseInt(
                                accessory.qty
                            ) || 0;


                        let stock =
                            parseInt(
                                accessory.stok
                            ) || 0;


                        if (

                            stock < 1 ||

                            qty < 1 ||

                            qty > stock

                        ) {

                            invalidStock =
                                true;

                        }

                    }
                );


                if (invalidStock) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Stok Accessories Tidak Valid',

                        text:
                            'Terdapat accessories dengan stok tidak mencukupi.',

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   SYNC
                ====================================================== */

                syncTableData();


                /* =====================================================
                   VALIDASI HARGA
                ====================================================== */

                let priceValidation =
                    validateMinimumPrice();


                if (
                    !priceValidation.valid
                ) {

                    let productName =
                        priceValidation.name;


                    let extraText =
                        '';


                    if (
                        priceValidation.type ===
                        'item'
                    ) {

                        extraText =

                            '<br>No Seri: <strong>' +

                            escapeHtml(
                                priceValidation.no_seri
                            ) +

                            '</strong>';

                    }


                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Harga Tidak Valid',

                        html: `

                            Harga jual

                            <strong>
                                ${escapeHtml(
                            productName
                        )}
                            </strong>

                            tidak boleh lebih kecil dari harga bottom.

                            ${extraText}

                            <br><br>

                            Harga Minimum:

                            <strong>
                                ${formatRupiah(
                            priceValidation.minimum
                        )}
                            </strong>

                            <br>

                            Harga Jual:

                            <strong>
                                ${formatRupiah(
                            priceValidation.price
                        )}
                            </strong>

                        `,

                        confirmButtonText:
                            'OK'

                    });

                    return;

                }


                /* =====================================================
                   LOCK SAVE
                ====================================================== */

                isSaving =
                    true;


                let saveButton =
                    $('#btnSave');


                saveButton
                    .prop(
                        'disabled',
                        true
                    )
                    .html(`

                        <i class="bx bx-loader-alt bx-spin"></i>

                        Menyimpan...

                    `);


                /* =====================================================
                   AJAX SAVE
                ====================================================== */

                $.ajax({

                    url:
                        '{{ route('manager.sale.store') }}',

                    method:
                        'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        divisi_id:
                        selectedDivisionId,

                        customer_id:
                        customerId,
                        kode: $('select[name="kode"]').val(),
                        bank_id:
                            $('#bank').val(),

                        total_item:
                            $('#total_item').val(),

                        total_price:
                            parseRupiah(
                                $('#totalrp').val()
                            ),

                        ongkir:
                            parseRupiah(
                                $('#ongkir').val()
                            ),

                        diskon:
                            parseRupiah(
                                $('#diskon').val()
                            ),

                        admin_fee:
                            parseRupiah(
                                $('#admin_fee').val()
                            ),

                        nominal_in:
                            parseRupiah(
                                $('#nominal_in').val()
                            ),

                        ppn:
                            parseRupiah(
                                $('#ppn').val()
                            ),

                        pph:
                            parseRupiah(
                                $('#pph').val()
                            ),

                        deadlines:
                            $('#deadlines').val(),

                        date_pay:
                            $('#date_pay').val(),

                        created_at:
                            $('#created_at').val(),

                        no_po:
                            $('#no_po').val(),

                        penerima:
                            $('#penerima').val(),

                        description:

                            $('input[name="payment_method"]:checked')
                                .val() === 'cash'

                                ? 'Cash'

                                : null,

                        inv_manual:
                            $('#inv_manual').val(),

                        bayar:
                            parseRupiah(
                                $('#bayarrp').val()
                            ),

                        payment_method:

                            $('input[name="payment_method"]:checked')
                                .val(),

                        accessories:
                        accessoriesData,

                        items:
                        itemsData

                    },


                    success:
                        function (response) {

                            if (

                                response &&

                                response.status ===
                                'success'

                            ) {

                                Swal.fire({

                                    icon:
                                        'success',

                                    title:
                                        'Berhasil',

                                    text:
                                        response.message ||
                                        'Transaksi berhasil disimpan.',

                                    showConfirmButton:
                                        false,

                                    timer:
                                        1500

                                });


                                setTimeout(
                                    function () {

                                        window.location.reload();

                                    },
                                    1500
                                );


                                return;

                            }


                            Swal.fire({

                                icon:
                                    'error',

                                title:
                                    'Transaksi Gagal',

                                text:
                                    response?.message ||
                                    'Transaksi tidak dapat disimpan.',

                                confirmButtonText:
                                    'OK'

                            });


                            isSaving =
                                false;


                            saveButton
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html(`

                                    <i class="bx bx-check-circle"></i>

                                    <span class="btn-save-text">
                                        Simpan Transaksi
                                    </span>

                                `);

                        },


                    error:
                        function (xhr) {

                            console.error(
                                'Save Error:',
                                xhr.responseText
                            );


                            let errorMessage =
                                'Terjadi kesalahan saat menyimpan transaksi.';


                            if (

                                xhr.responseJSON &&

                                xhr.responseJSON.message

                            ) {

                                errorMessage =
                                    xhr.responseJSON.message;

                            }


                            if (

                                xhr.responseJSON &&

                                xhr.responseJSON.errors

                            ) {

                                let errors =
                                    xhr.responseJSON.errors;


                                let messages =
                                    [];


                                Object.keys(errors)
                                    .forEach(
                                        function (field) {

                                            let fieldErrors =
                                                errors[field];


                                            if (
                                                Array.isArray(
                                                    fieldErrors
                                                )
                                            ) {

                                                fieldErrors
                                                    .forEach(
                                                        function (message) {

                                                            messages.push(
                                                                message
                                                            );

                                                        }
                                                    );

                                            } else {

                                                messages.push(
                                                    fieldErrors
                                                );

                                            }

                                        }
                                    );


                                if (
                                    messages.length > 0
                                ) {

                                    errorMessage =
                                        messages.join(
                                            '<br>'
                                        );

                                }

                            }


                            Swal.fire({

                                icon:
                                    'error',

                                title:
                                    'Transaksi Gagal',

                                html:
                                errorMessage,

                                confirmButtonText:
                                    'OK'

                            });


                            isSaving =
                                false;


                            saveButton
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html(`

                                    <i class="bx bx-check-circle"></i>

                                    <span class="btn-save-text">
                                        Simpan Transaksi
                                    </span>

                                `);

                        }

                });

            }


            /* =========================================================
               BUTTON SAVE
            ========================================================= */

            $('#btnSave').on(
                'click',
                function (e) {

                    e.preventDefault();


                    if (isSaving) {

                        return;

                    }


                    saveTransaction();

                }
            );


            /* =========================================================
               ESCAPE HTML
            ========================================================= */

            function escapeHtml(text) {

                return String(
                    text ?? ''
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
                        '&#039;'
                    );

            }


            /* =========================================================
               ESCAPE ATTRIBUTE
            ========================================================= */

            function escapeAttribute(text) {

                return String(
                    text ?? ''
                )

                    .replace(
                        /&/g,
                        '&amp;'
                    )

                    .replace(
                        /"/g,
                        '&quot;'
                    )

                    .replace(
                        /'/g,
                        '&#039;'
                    )

                    .replace(
                        /</g,
                        '&lt;'
                    )

                    .replace(
                        />/g,
                        '&gt;'
                    );

            }


            /* =========================================================
               INITIAL
            ========================================================= */

            updateDivisionState();

            calculateTotal();

            updatePaymentMethod();

        });

    </script>

@endpush
