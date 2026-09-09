@extends('layouts.master')

@section('title', 'EDIT TRANSAKSI')

@section('content')

    <style>

        /* =========================================================
           PAGE
        ========================================================= */

        .edit-sale-page {
            padding: 8px 10px 20px;
        }


        /* =========================================================
           DIVISI
        ========================================================= */

        .division-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 16px;
            margin-bottom: 16px;
        }


        /* =========================================================
           LABEL
        ========================================================= */

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .required {
            color: #dc3545;
        }


        /* =========================================================
           CARD
        ========================================================= */

        .edit-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            overflow: hidden;
            height: 100%;
        }

        .edit-card-header {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            background: #ffffff;
        }

        .edit-card-body {
            padding: 16px;
        }


        /* =========================================================
           TRANSACTION
        ========================================================= */

        .transaction-meta {
            margin-bottom: 15px;
        }

        .invoice-title {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .invoice-value {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }


        /* =========================================================
           SCAN
        ========================================================= */

        .scan-group {
            display: flex;
            width: 100%;
        }

        .scan-icon {
            width: 43px;
            min-width: 43px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #dee2e6;
            border-right: 0;

            border-radius: 5px 0 0 5px;

            color: #6b7280;
            background: #ffffff;
        }

        .scan-group .form-control {
            border-radius: 0;
        }

        .btn-search-code {
            height: 38px;
            min-width: 75px;
            border-radius: 0 5px 5px 0;
        }

        .scan-help {
            margin-top: 4px;
            font-size: 11px;
            color: #7b8490;
        }


        /* =========================================================
           PRODUCT
        ========================================================= */

        .product-picker {
            margin-top: 13px;
            margin-bottom: 8px;
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .table-sale {
            width: 100% !important;
            margin-top: 8px !important;
            margin-bottom: 0 !important;
        }

        .table-sale thead th {
            font-size: 12px;
            font-weight: 600;
            color: #374151;

            padding: 10px 7px;

            border-top: 0 !important;
            border-bottom: 1px solid #dee2e6 !important;

            white-space: nowrap;
        }

        .table-sale tbody td {
            font-size: 12px;
            color: #374151;

            padding: 8px 7px;

            vertical-align: middle;

            border-bottom: 1px solid #edf0f2;
        }

        .table-sale .form-control {
            min-height: 34px;
            font-size: 12px;
            padding: 5px 8px;
        }

        .price-input {
            text-align: right;
        }

        .qty-input {
            text-align: center;
        }

        .btn-delete {
            width: 32px;
            height: 32px;

            padding: 0;

            display: inline-flex;
            align-items: center;
            justify-content: center;
        }


        /* =========================================================
           PAYMENT
        ========================================================= */

        .payment-field {
            margin-bottom: 10px;
        }

        .payment-field label {
            display: block;

            font-size: 12px;
            font-weight: 600;

            color: #374151;

            margin-bottom: 5px;
        }

        .payment-field .form-control {
            min-height: 35px;
            font-size: 12px;
        }

        .readonly-field {
            background: #f8f9fa !important;
        }

        .payment-divider {
            margin: 14px 0;

            border: 0;
            border-top: 1px solid #e5e7eb;
        }


        /* =========================================================
           PAYMENT METHOD
        ========================================================= */

        .payment-method-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;

            margin-bottom: 9px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 20px;

            font-size: 12px;

            margin-bottom: 13px;
        }

        .payment-method label {
            display: flex;
            align-items: center;

            margin: 0;

            font-size: 12px;
            font-weight: 500;

            cursor: pointer;
        }

        .payment-method input {
            margin-right: 5px;
        }

        .payment-method i {
            margin-right: 3px;
        }


        /* =========================================================
           TRANSFER
        ========================================================= */

        #transfer-payment-fields {
            display: none;
        }


        /* =========================================================
           BUTTON
        ========================================================= */

        .btn-save {
            width: 100%;
            height: 36px;

            font-size: 13px;
            font-weight: 500;

            border-radius: 5px;
        }

        .btn-cancel {
            width: 100%;
            height: 36px;

            margin-top: 7px;

            font-size: 13px;

            border-radius: 5px;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 991px) {

            .payment-column {
                margin-top: 16px;
            }

        }

    </style>


    <div class="edit-sale-page">


        {{-- =========================================================
             DIVISI
        ========================================================== --}}

        <div class="division-card">

            <label class="field-label">

                Divisi

                <span class="required">*</span>

            </label>


            <select
                name="divisi_id"
                id="single-select-optgroup-field"
                data-placeholder="-- Pilih Divisi --"
                class="form-control accessory-select">

                <option value=""></option>

                @foreach($divisi as $div)

                    <option
                        value="{{ $div->id }}"
                        {{ (int) $sale->divisi_id === (int) $div->id ? 'selected' : '' }}>

                        {{ $div->name }}

                    </option>

                @endforeach

            </select>

        </div>


        {{-- =========================================================
             MAIN CONTENT
        ========================================================== --}}

        <div class="row g-3">


            {{-- =====================================================
                 DETAIL TRANSAKSI
            ====================================================== --}}

            <div class="col-lg-8">

                <div class="edit-card">

                    <div class="edit-card-header">
                        Detail Transaksi
                    </div>


                    <div class="edit-card-body">


                        {{-- =================================================
                             INVOICE / CUSTOMER
                        ================================================== --}}

                        <div class="row">


                            {{-- INVOICE --}}

                            <div class="col-md-6 mb-3">

                                <label class="field-label">
                                    Invoice
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $sale->invoice }}"
                                    readonly>

                            </div>


                            {{-- CUSTOMER --}}

                            <div class="col-md-6 mb-3">

                                <label class="field-label">
                                    Customer
                                </label>

                                <select
                                    name="customer_id"
                                    id="customer_id"
                                    data-placeholder="-- Pilih Customer --"
                                    class="form-control accessory-select">

                                    <option value=""></option>

                                    @foreach($customers as $customer)

                                        <option
                                            value="{{ $customer->id }}"
                                            {{ (int) $sale->customer_id === (int) $customer->id ? 'selected' : '' }}>

                                            {{ $customer->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- TANGGAL TRANSAKSI --}}

                            <div class="col-md-6 mb-3">

                                <label class="field-label">
                                    Tanggal Transaksi
                                </label>

                                <input
                                    type="text"
                                    name="created_at"
                                    id="created_at"
                                    class="form-control datepicker"
                                    placeholder="Tanggal Invoice"
                                    value="{{ $sale->created_at }}">

                            </div>


                            {{-- NOMOR PO --}}

                            <div class="col-md-6 mb-3">

                                <label class="field-label">
                                    Nomor PO
                                </label>

                                <input
                                    type="text"
                                    name="no_po"
                                    id="no_po"
                                    class="form-control"
                                    placeholder="Nomor PO"
                                    value="{{ $sale->no_po }}">

                            </div>


                            {{-- DEADLINE --}}

                            <div class="col-md-6 mb-3">

                                <label class="field-label">
                                    Tanggal Pembayaran Pertama
                                </label>
                                @php
                                    $debt = $sale->debt->first();
                                @endphp

                                <input
                                    type="text"
                                    name="date_pay"
                                    id="date_pay"
                                    class="form-control datepicker"
                                    placeholder="Tanggal Pembayaran"
                                    value="{{ $debt?->date_pay ?? '' }}"
                                    autocomplete="off">

                            </div>

                        </div>


                        {{-- =================================================
                             SCAN BARCODE
                        ================================================== --}}

                        <div>

                            <label class="field-label">
                                Scan Barcode
                            </label>


                            <div class="scan-group">

                                <div class="scan-icon">

                                    <i class="bx bx-barcode"></i>

                                </div>


                                <input
                                    type="text"
                                    class="form-control"
                                    id="code"
                                    placeholder="Scan barcode produk..."
                                    autocomplete="off">


                                <button
                                    type="button"
                                    id="btn-search-code"
                                    class="btn btn-primary btn-search-code">

                                    <i class="bx bx-search"></i>


                                </button>

                            </div>


                            <div class="scan-help">

                                Produk yang dapat dipilih hanya produk
                                dari divisi transaksi ini.

                            </div>

                        </div>


                        {{-- =================================================
                             PRODUCT PICKER
                        ================================================== --}}

                        <div class="product-picker">

                            <label class="field-label">
                                Atau Pilih Produk
                            </label>


                            <select
                                id="product_picker"
                                class="form-control"
                                style="width: 100%;">

                                <option value=""></option>


                                {{-- ACCESSORIES --}}

                                @if(isset($accessories) && $accessories->count())

                                    <optgroup label="ACCESSORIES">

                                        @foreach($accessories as $accessory)

                                            @if($accessory->stok > 0)

                                                <option
                                                    value="accessory|{{ $accessory->id }}"
                                                    data-type="accessory"
                                                    data-id="{{ $accessory->id }}"
                                                    data-divisi-id="{{ $accessory->divisi_id }}"
                                                    data-code="{{ $accessory->code_acces }}"
                                                    data-name="{{ $accessory->name }}"
                                                    data-price="{{ $accessory->price ?? 0 }}"
                                                    data-price-bottom="{{ $accessory->price_bottom ?? 0 }}"
                                                    data-stok="{{ $accessory->stok ?? 0 }}"
                                                    data-capital-price="{{ $accessory->capital_price ?? 0 }}">

                                                    {{ $accessory->code_acces }}
                                                    -
                                                    {{ $accessory->name }}
                                                    -
                                                    Stok:
                                                    {{ $accessory->stok }}

                                                </option>

                                            @endif

                                        @endforeach

                                    </optgroup>

                                @endif


                                {{-- ITEM --}}

                                @if(isset($item) && $item->count())

                                    <optgroup label="ALAT / ITEM">

                                        @foreach($item as $data)

                                            @if($data->status != 2)

                                                <option
                                                    value="item|{{ $data->id }}"
                                                    data-type="item"
                                                    data-id="{{ $data->id }}"
                                                    data-divisi-id="{{ $data->divisi_id }}"
                                                    data-no-seri="{{ $data->no_seri }}"
                                                    data-name="{{ $data->name }}"
                                                    data-price="{{ $data->price ?? 0 }}"
                                                    data-price-bottom="{{ $data->price_bottom ?? 0 }}"
                                                    data-itemcategory-id="{{ $data->itemcategory_id }}"
                                                    data-capital-price="{{ $data->capital_price ?? 0 }}">

                                                    {{ $data->no_seri }}
                                                    -
                                                    {{ $data->name }}

                                                </option>

                                            @endif

                                        @endforeach

                                    </optgroup>

                                @endif

                            </select>

                        </div>


                        {{-- =================================================
                             TABLE
                        ================================================== --}}

                        <div class="table-responsive">

                            <table class="table table-sale">

                                <thead>

                                <tr>

                                    <th width="16%">
                                        Kode / No Seri
                                    </th>

                                    <th width="23%">
                                        Nama
                                    </th>

                                    <th width="15%">
                                        Harga Terendah
                                    </th>

                                    <th width="16%">
                                        Harga Jual
                                    </th>

                                    <th width="9%">
                                        Qty
                                    </th>

                                    <th width="8%">
                                        Status
                                    </th>

                                    <th width="8%">
                                        Aksi
                                    </th>

                                </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>


                        <input
                            type="hidden"
                            id="total_item"
                            value="0">

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 PEMBAYARAN
            ====================================================== --}}

            <div class="col-lg-4 payment-column">

                <div class="edit-card">

                    <div class="edit-card-header">
                        Pembayaran
                    </div>


                    <div class="edit-card-body">

                        <div class="row">


                            {{-- SUBTOTAL --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Subtotal
                                    </label>

                                    <input
                                        type="text"
                                        id="totalrp"
                                        readonly
                                        class="form-control readonly-field"
                                        value="{{ formatRupiah($sale->total_price ?? 0) }}">

                                </div>

                            </div>


                            {{-- DISKON --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Diskon
                                    </label>

                                    <input
                                        type="text"
                                        id="diskon"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->diskon ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- PPN --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        PPN
                                    </label>

                                    <input
                                        type="text"
                                        id="ppn"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->ppn ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- PPH --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        PPH
                                    </label>

                                    <input
                                        type="text"
                                        id="pph"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->pph ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- ONGKIR --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Ongkir Konsumen
                                    </label>

                                    <input
                                        type="text"
                                        id="ongkir"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->ongkir ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- ADMIN FEE --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Biaya Admin
                                    </label>

                                    <input
                                        type="text"
                                        id="admin_fee"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->admin_fee ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- GRAND TOTAL --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Grand Total
                                    </label>

                                    <input
                                        type="text"
                                        id="bayarrp"
                                        readonly
                                        class="form-control readonly-field"
                                        value="{{ formatRupiah($sale->pay ?? 0) }}">

                                </div>

                            </div>


                            {{-- NOMINAL IN --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Nominal In
                                    </label>

                                    <input
                                        type="text"
                                        id="nominal_in"
                                        class="form-control rupiah-input"
                                        value="{{ formatRupiah($sale->nominal_in ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- FEE --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Fee
                                    </label>

                                    <input
                                        type="text"
                                        id="fee"
                                        class="form-control"
                                        value="{{ formatRupiah($sale->fee ?? 0) }}"
                                        autocomplete="off">

                                </div>

                            </div>


                            {{-- PAY PLAN --}}

                            <div class="col-6">

                                <div class="payment-field">

                                    <label>
                                        Pay Plan
                                    </label>

                                    <input
                                        type="text"
                                        id="payment_plan"
                                        class="form-control datepicker"
                                        placeholder="Tanggal Pay Plan"
                                        value="{{ $sale->deadlines }}">

                                </div>

                            </div>

                        </div>


                        <hr class="payment-divider">


                        {{-- =================================================
                             PAYMENT METHOD
                        ================================================== --}}

                        <div class="payment-method-title">
                            Metode Pembayaran
                        </div>


                        @php

                            $debt = $sale->debt->first();

                            $selectedBankId =
                                $debt->bank_id ?? null;

                            $selectedPenerima =
                                $debt->penerima ?? '';

                            $selectedDescription =
                                $debt->description ?? '';

                            $isTransfer =
                                !empty($selectedBankId);

                        @endphp


                        <div class="payment-method">

                            {{-- CASH --}}

                            <label>

                                <input
                                    type="radio"
                                    name="payment_method"
                                    id="payment_cash"
                                    value="cash"
                                    {{ !$isTransfer ? 'checked' : '' }}>

                                <i class="bx bx-money"></i>

                                Cash

                            </label>


                            {{-- TRANSFER --}}

                            <label>

                                <input
                                    type="radio"
                                    name="payment_method"
                                    id="payment_transfer"
                                    value="transfer"
                                    {{ $isTransfer ? 'checked' : '' }}>

                                <i class="bx bx-transfer"></i>

                                Transfer Bank

                            </label>

                        </div>


                        {{-- =================================================
                             TRANSFER FIELDS
                        ================================================== --}}

                        <div
                            id="transfer-payment-fields"
                            style="{{ $isTransfer ? '' : 'display:none;' }}">

                            <div class="row">


                                {{-- BANK --}}

                                <div class="col-6">

                                    <div class="payment-field">

                                        <label>
                                            Nama Bank
                                        </label>

                                        <select
                                            id="bank"
                                            class="form-control accessory-select">

                                            <option value="">
                                                -- Pilih Bank --
                                            </option>

                                            @foreach($bank as $data)

                                                <option
                                                    value="{{ $data->id }}"
                                                    {{ (int) $selectedBankId === (int) $data->id ? 'selected' : '' }}>

                                                    {{ $data->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                </div>


                                {{-- PENERIMA --}}

                                <div class="col-6">

                                    <div class="payment-field">

                                        <label>
                                            Nama Penerima
                                        </label>

                                        <input
                                            type="text"
                                            id="penerima"
                                            class="form-control"
                                            placeholder="Nama penerima"
                                            value="{{ $selectedPenerima }}">

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                             SAVE
                        ================================================== --}}

                        <button
                            type="button"
                            class="btn btn-primary btn-save btn-simpan">

                            <i class="bx bx-check-circle"></i>

                            Simpan Perubahan

                        </button>


                        {{-- =================================================
                             CANCEL
                        ================================================== --}}

                        <a
                            href="{{ url()->previous() }}"
                            class="btn btn-secondary btn-cancel">

                            Batal

                        </a>

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


            /* =========================================================
               SELECT2
            ========================================================= */

            $('#single-select-optgroup-field').select2({

                theme: 'bootstrap-5',

                placeholder: '-- Pilih Divisi --',

                width: '100%',

                allowClear: true

            });


            $('#customer_id').select2({

                theme: 'bootstrap-5',

                placeholder: '-- Pilih Customer --',

                width: '100%',

                allowClear: true

            });


            $('#bank').select2({

                theme: 'bootstrap-5',

                placeholder: '-- Pilih Bank --',

                width: '100%',

                allowClear: true

            });


            $('#product_picker').select2({

                theme: 'bootstrap-5',

                placeholder: '-- Pilih Produk --',

                width: '100%',

                allowClear: true

            });


            /* =========================================================
               DIVISI TRANSAKSI
            ========================================================= */

            let originalDivisiId =
                String(
                    $('#single-select-optgroup-field').val() || ''
                );


            /* =========================================================
               PAYMENT METHOD
            ========================================================= */

            function togglePaymentMethod() {

                let method =
                    $('input[name="payment_method"]:checked').val();


                if (method === 'transfer') {

                    $('#transfer-payment-fields')
                        .stop(true, true)
                        .slideDown(150);

                } else {

                    $('#transfer-payment-fields')
                        .stop(true, true)
                        .slideUp(150);


                    /*
                     * Saat CASH dipilih,
                     * bank dan penerima dikosongkan.
                     */

                    $('#bank')
                        .val(null)
                        .trigger('change');

                    $('#penerima')
                        .val('');

                }

            }


            $('input[name="payment_method"]').on(
                'change',
                togglePaymentMethod
            );


            /*
             * Jalankan saat halaman pertama kali dibuka.
             */

            togglePaymentMethod();


            /* =========================================================
               DATATABLE
            ========================================================= */

            let table =
                $('.table-sale').DataTable({

                    processing: true,

                    searching: false,

                    paging: false,

                    info: false,

                    ordering: false,

                    autoWidth: false,

                    language: {

                        emptyTable:
                            'Tidak ada barang dalam transaksi'

                    },

                    columns: [

                        {
                            data: 'code',

                            render: function (data) {

                                return data ?? '-';

                            }

                        },


                        {
                            data: 'name',

                            render: function (data) {

                                return data ?? '-';

                            }

                        },


                        {
                            data: 'price_bottom',

                            render: function (data) {

                                return formatRupiah(data);

                            }

                        },


                        {
                            data: 'price_sale',

                            render: function (data) {

                                let price =
                                    parseFloat(data) || 0;

                                return `
                                    <input
                                        type="text"
                                        class="form-control price-input"
                                        value="${formatRupiah(price)}"
                                        autocomplete="off">
                                `;

                            }

                        },


                        {
                            data: 'qty',

                            render: function (data, type, row) {

                                let qty =
                                    parseInt(data) || 1;


                                if (row.type === 'item') {

                                    return `
                                        <input
                                            type="number"
                                            class="form-control qty-input"
                                            min="1"
                                            max="1"
                                            value="1"
                                            readonly>
                                    `;

                                }


                                return `
                                    <input
                                        type="number"
                                        class="form-control qty-input"
                                        min="1"
                                        value="${qty}">
                                `;

                            }

                        },


                        {
                            data: 'status',

                            render: function (data) {

                                if (data === 'new') {

                                    return `
                                        <span class="badge bg-success">
                                            NEW
                                        </span>
                                    `;

                                }


                                if (data === 'return') {

                                    return `
                                        <span class="badge bg-danger">
                                            RETURN
                                        </span>
                                    `;

                                }


                                return `
                                    <span class="badge bg-primary">
                                        OLD
                                    </span>
                                `;

                            }

                        },


                        {
                            data: null,

                            orderable: false,

                            searchable: false,

                            render: function (data, type, row) {

                                if (row.status === 'return') {

                                    return `
                                        <span class="text-muted">
                                            -
                                        </span>
                                    `;

                                }


                                return `
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-delete"
                                        data-id="${row.sale_detail_id ?? ''}"
                                        data-type="${row.type ?? ''}">

                                        <i class="bx bx-trash"></i>

                                    </button>
                                `;

                            }

                        }

                    ]

                });


            /* =========================================================
               DELETED DATA
            ========================================================= */

            let deletedAccessories = [];

            let deletedItems = [];


            /* =========================================================
               ACCESSORIES LAMA
            ========================================================= */

            let accessories =
                @json($sale->accessoriesSales);


            accessories.forEach(function (item) {

                let priceSale =
                    parseFloat(
                        item.price_sale ?? 0
                    ) || 0;


                let qty =
                    parseInt(
                        item.qty ?? 0
                    ) || 0;


                table.row.add({

                    code:
                        item.accessories?.code_acces ?? '-',

                    name:
                        item.accessories?.name ?? '-',

                    price_bottom:
                        parseFloat(
                            item.accessories?.price_bottom ?? 0
                        ) || 0,

                    price_sale:
                    priceSale,

                    price:
                    priceSale,

                    qty:
                    qty,

                    accessories_id:
                    item.accessories_id,

                    sale_detail_id:
                    item.id,

                    type:
                        'accessory',

                    status:
                        item.status_return == 1
                            ? 'return'
                            : 'old',

                    return_qty:
                        parseInt(
                            item.return_qty ?? 0
                        ) || 0,

                    capital_price:
                        parseFloat(
                            item.accessories?.capital_price ?? 0
                        ) || 0

                }).draw(false);

            });


            /* =========================================================
               ITEMS LAMA
            ========================================================= */

            let items =
                @json($sale->itemSales);


            items.forEach(function (item) {

                let price =
                    parseFloat(
                        item.price ?? 0
                    ) || 0;


                table.row.add({

                    code:
                        item.no_seri ?? '-',

                    name:
                        item.name ?? '-',

                    price_bottom:
                        parseFloat(
                            item.price_bottom ?? 0
                        ) || 0,

                    price_sale:
                    price,

                    price:
                    price,

                    qty:
                        1,

                    itemcategory_id:
                    item.itemcategory_id,

                    sale_detail_id:
                    item.id,

                    type:
                        'item',

                    status:
                        item.status_return == 1
                            ? 'return'
                            : 'old',

                    capital_price:
                        parseFloat(
                            item.capital_price ?? 0
                        ) || 0

                }).draw(false);

            });


            /* =========================================================
               RUPIAH TO NUMBER
            ========================================================= */


            function rupiahToNumber(value) {

                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {
                    return 0;
                }

                return parseInt(
                    value
                        .toString()
                        .replace(/[^0-9]/g, '')
                ) || 0;
            }


            function formatRupiah(value) {

                let number = rupiahToNumber(value);

                return 'Rp. ' + number.toLocaleString('id-ID');
            }


            /* =========================================================
               NOMINAL IN - FORMAT RUPIAH
            ========================================================= */

            $(document).on('input', '#nominal_in', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#fee', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#admin_fee', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#diskon', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#ppn', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#pph', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });
            $(document).on('input', '#ongkir', function () {

                let value = rupiahToNumber($(this).val());

                $(this).val(formatRupiah(value));

            });


            /* =========================================================
               CALCULATE TOTAL
            ========================================================= */

            function calculateTotal() {

                let total = 0;

                let totalQty = 0;


                table.rows().every(function () {

                    let rowApi = this;

                    let node =
                        rowApi.node();


                    if (!node) {
                        return;
                    }


                    let data =
                        rowApi.data();


                    if (!data) {
                        return;
                    }


                    /*
                     * RETURN tidak masuk total
                     */

                    if (data.status === 'return') {
                        return;
                    }


                    let qty =
                        parseInt(
                            $(node)
                                .find('.qty-input')
                                .val()
                        ) || 0;


                    let price =
                        rupiahToNumber(
                            $(node)
                                .find('.price-input')
                                .val()
                        );


                    total +=
                        qty * price;


                    totalQty +=
                        qty;

                });


                let bayar =
                    total;


                bayar -=
                    rupiahToNumber(
                        $('#diskon').val()
                    );


                bayar +=
                    rupiahToNumber(
                        $('#ongkir').val()
                    );


                bayar +=
                    rupiahToNumber(
                        $('#ppn').val()
                    );


                bayar -=
                    rupiahToNumber(
                        $('#pph').val()
                    );


                bayar -=
                    rupiahToNumber(
                        $('#admin_fee').val()
                    );



                if (bayar < 0) {
                    bayar = 0;
                }


                $('#totalrp').val(
                    formatRupiah(total)
                );


                $('#bayarrp').val(
                    formatRupiah(bayar)
                );


                $('#total_item').val(
                    totalQty
                );

            }


            /* =========================================================
               INITIAL CALCULATE
            ========================================================= */

            calculateTotal();


            /* =========================================================
               PRICE INPUT FORMAT
            ========================================================= */

            $(document).on(
                'input',
                '.price-input',
                function () {

                    let value =
                        rupiahToNumber(
                            $(this).val()
                        );


                    $(this).val(
                        formatRupiah(value)
                    );


                    calculateTotal();

                }
            );


            /* =========================================================
               UPDATE TOTAL
            ========================================================= */

            $(document).on(

                'input',

                '.price-input, .qty-input, #diskon, #ongkir, #ppn, #pph, #admin_fee, #fee',

                function () {

                    calculateTotal();

                }

            );


            /* =========================================================
               FIND EXISTING PRODUCT
            ========================================================= */

            function findExistingProduct(
                type,
                identifier
            ) {

                let result = null;


                table.rows().every(function () {

                    let rowApi = this;

                    let data =
                        rowApi.data();


                    if (!data) {
                        return;
                    }


                    if (data.status === 'return') {
                        return;
                    }


                    /* ACCESSORIES */

                    if (type === 'accessory') {

                        if (
                            String(
                                data.accessories_id ?? ''
                            ) ===
                            String(
                                identifier ?? ''
                            )
                        ) {

                            result = {

                                rowApi:
                                rowApi,

                                data:
                                data

                            };

                        }

                    }


                    /* ITEM */

                    if (type === 'item') {

                        if (
                            String(
                                data.code ?? ''
                            ).trim() ===
                            String(
                                identifier ?? ''
                            ).trim()
                        ) {

                            result = {

                                rowApi:
                                rowApi,

                                data:
                                data

                            };

                        }

                    }

                });


                return result;

            }


            /* =========================================================
               ADD ACCESSORY
            ========================================================= */

            function addAccessory(data) {

                if (!data) {
                    return;
                }


                let accessoryId =
                    data.id ??
                    data.accessories_id;


                if (!accessoryId) {

                    Swal.fire(
                        'Error',
                        'ID accessories tidak ditemukan.',
                        'error'
                    );

                    return;

                }


                let selectedDivisi =
                    String(
                        $('#single-select-optgroup-field').val() || ''
                    );


                let productDivisi =
                    String(
                        data.divisi_id ?? ''
                    );


                if (
                    selectedDivisi &&
                    productDivisi &&
                    selectedDivisi !== productDivisi
                ) {

                    Swal.fire(
                        'Divisi Tidak Sesuai',
                        'Accessories bukan berasal dari divisi transaksi.',
                        'warning'
                    );

                    return;

                }


                let existing =
                    findExistingProduct(
                        'accessory',
                        accessoryId
                    );


                if (existing) {

                    let rowNode =
                        existing.rowApi.node();


                    let qtyInput =
                        $(rowNode)
                            .find('.qty-input');


                    let currentQty =
                        parseInt(
                            qtyInput.val()
                        ) || 0;


                    let newQty =
                        currentQty + 1;


                    let stok =
                        parseInt(
                            data.stok ?? 0
                        ) || 0;


                    if (
                        stok > 0 &&
                        newQty > stok
                    ) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Stok Tidak Mencukupi',

                            text:
                                'Stok accessories hanya tersedia ' +
                                stok +
                                ' pcs.'

                        });

                        return;

                    }


                    qtyInput.val(newQty);

                    existing.data.qty =
                        newQty;


                    existing.rowApi
                        .data(existing.data);


                    existing.rowApi
                        .invalidate();


                    calculateTotal();

                    return;

                }


                let price =
                    parseFloat(
                        data.price ?? 0
                    ) || 0;


                let priceBottom =
                    parseFloat(
                        data.price_bottom ?? 0
                    ) || 0;


                let capitalPrice =
                    parseFloat(
                        data.capital_price ?? 0
                    ) || 0;


                table.row.add({

                    code:
                        data.code_acces ??
                        data.code ??
                        '-',

                    name:
                        data.name ??
                        '-',

                    price_bottom:
                    priceBottom,

                    price_sale:
                    price,

                    price:
                    price,

                    qty:
                        1,

                    accessories_id:
                    accessoryId,

                    sale_detail_id:
                        null,

                    type:
                        'accessory',

                    status:
                        'new',

                    return_qty:
                        0,

                    capital_price:
                    capitalPrice

                }).draw(false);


                calculateTotal();

            }


            /* =========================================================
               ADD ITEM
            ========================================================= */

            function addItem(data) {

                if (!data) {
                    return;
                }


                let noSeri =
                    String(
                        data.no_seri ?? ''
                    ).trim();


                if (!noSeri) {

                    Swal.fire(
                        'Error',
                        'Nomor seri item tidak ditemukan.',
                        'error'
                    );

                    return;

                }


                let selectedDivisi =
                    String(
                        $('#single-select-optgroup-field').val() || ''
                    );


                let productDivisi =
                    String(
                        data.divisi_id ?? ''
                    );


                if (
                    selectedDivisi &&
                    productDivisi &&
                    selectedDivisi !== productDivisi
                ) {

                    Swal.fire(
                        'Divisi Tidak Sesuai',
                        'Item bukan berasal dari divisi transaksi.',
                        'warning'
                    );

                    return;

                }


                let existing =
                    findExistingProduct(
                        'item',
                        noSeri
                    );


                if (existing) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'Produk Sudah Ada',

                        text:
                            'Alat tersebut sudah ada dalam transaksi.'

                    });

                    return;

                }


                let price =
                    parseFloat(
                        data.price ?? 0
                    ) || 0;


                let priceBottom =
                    parseFloat(
                        data.price_bottom ?? 0
                    ) || 0;


                let capitalPrice =
                    parseFloat(
                        data.capital_price ?? 0
                    ) || 0;


                table.row.add({

                    code:
                    noSeri,

                    name:
                        data.name ??
                        '-',

                    price_bottom:
                    priceBottom,

                    price_sale:
                    price,

                    price:
                    price,

                    qty:
                        1,

                    itemcategory_id:
                    data.itemcategory_id,

                    sale_detail_id:
                        null,

                    type:
                        'item',

                    status:
                        'new',

                    capital_price:
                    capitalPrice

                }).draw(false);


                calculateTotal();

            }


            /* =========================================================
               PRODUCT PICKER
            ========================================================= */

            $('#product_picker').on(
                'select2:select',
                function (e) {

                    let option =
                        $(e.params.data.element);


                    if (!option.length) {
                        return;
                    }


                    let type =
                        option.attr(
                            'data-type'
                        );


                    let divisiId =
                        String(
                            $('#single-select-optgroup-field').val() || ''
                        );


                    if (!divisiId) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Divisi Belum Dipilih',

                            text:
                                'Silakan pilih divisi terlebih dahulu.'

                        });


                        $('#product_picker')
                            .val(null)
                            .trigger('change');

                        return;

                    }


                    let productDivisiId =
                        String(
                            option.attr(
                                'data-divisi-id'
                            ) || ''
                        );


                    if (
                        productDivisiId &&
                        productDivisiId !== divisiId
                    ) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Divisi Tidak Sesuai',

                            text:
                                'Produk bukan berasal dari divisi transaksi.'

                        });


                        $('#product_picker')
                            .val(null)
                            .trigger('change');

                        return;

                    }


                    /* ACCESSORIES */

                    if (type === 'accessory') {

                        addAccessory({

                            id:
                                option.attr('data-id'),

                            accessories_id:
                                option.attr('data-id'),

                            divisi_id:
                                option.attr('data-divisi-id'),

                            code_acces:
                                option.attr('data-code'),

                            name:
                                option.attr('data-name'),

                            price:
                                option.attr('data-price'),

                            price_bottom:
                                option.attr('data-price-bottom'),

                            stok:
                                option.attr('data-stok'),

                            capital_price:
                                option.attr('data-capital-price')

                        });

                    }


                    /* ITEM */

                    else if (type === 'item') {

                        addItem({

                            id:
                                option.attr('data-id'),

                            divisi_id:
                                option.attr('data-divisi-id'),

                            no_seri:
                                option.attr('data-no-seri'),

                            name:
                                option.attr('data-name'),

                            price:
                                option.attr('data-price'),

                            price_bottom:
                                option.attr('data-price-bottom'),

                            itemcategory_id:
                                option.attr('data-itemcategory-id'),

                            capital_price:
                                option.attr('data-capital-price')

                        });

                    }


                    $('#product_picker')
                        .val(null)
                        .trigger('change');

                }
            );


            /* =========================================================
               CHECK CODE
            ========================================================= */

            function checkCode() {

                let code =
                    $('#code')
                        .val()
                        .trim();


                if (!code) {
                    return;
                }


                let divisiId =
                    $('#single-select-optgroup-field')
                        .val();


                if (!divisiId) {

                    Swal.fire(
                        'Peringatan',
                        'Divisi belum dipilih.',
                        'warning'
                    );

                    return;

                }


                $.ajax({

                    url:
                        '{{ route("manager.sale.checkcode") }}',

                    method:
                        'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        code:
                        code,

                        divisi_id:
                        divisiId

                    },


                    success: function (response) {

                        if (
                            response.status !==
                            'success'
                        ) {

                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );

                            return;

                        }


                        if (
                            response.data?.divisi_id &&
                            String(
                                response.data.divisi_id
                            ) !==
                            String(divisiId)
                        ) {

                            Swal.fire(
                                'Divisi Tidak Sesuai',
                                'Produk bukan berasal dari divisi transaksi.',
                                'warning'
                            );

                            return;

                        }


                        if (
                            response.type ===
                            'accessory'
                        ) {

                            addAccessory(
                                response.data
                            );

                        } else {

                            addItem(
                                response.data
                            );

                        }


                        calculateTotal();


                        $('#code')
                            .val('')
                            .focus();

                    },


                    error: function (xhr) {

                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ??
                            'Gagal mengambil data barang.',
                            'error'
                        );

                    }

                });

            }


            /* =========================================================
               ENTER SCAN
            ========================================================= */

            $('#code').on(
                'keypress',
                function (e) {

                    if (e.which === 13) {

                        e.preventDefault();

                        checkCode();

                    }

                }
            );


            /* =========================================================
               BUTTON CARI
            ========================================================= */

            $('#btn-search-code').on(
                'click',
                function () {

                    checkCode();

                }
            );


            /* =========================================================
               DELETE / RETURN
            ========================================================= */

            $(document).on(
                'click',
                '.btn-delete',
                function () {

                    let row =
                        $(this).closest('tr');


                    let data =
                        table
                            .row(row)
                            .data();


                    if (!data) {
                        return;
                    }


                    /* BARANG BARU */

                    if (
                        data.status ===
                        'new'
                    ) {

                        table
                            .row(row)
                            .remove()
                            .draw(false);


                        calculateTotal();

                        return;

                    }


                    Swal.fire({

                        title:
                            'Hapus Barang?',

                        text:
                            'Barang akan diproses sebagai retur.',

                        icon:
                            'warning',

                        showCancelButton:
                            true,

                        confirmButtonText:
                            'Ya, Hapus',

                        cancelButtonText:
                            'Batal'

                    }).then(function (result) {

                        if (
                            !result.isConfirmed
                        ) {

                            return;

                        }


                        /* ACCESSORIES */

                        if (
                            data.type ===
                            'accessory'
                        ) {

                            deletedAccessories.push({

                                sale_detail_id:
                                data.sale_detail_id,

                                accessories_id:
                                data.accessories_id,

                                qty:
                                data.qty,

                                price_sale:
                                    parseFloat(
                                        data.price_sale ?? 0
                                    ) || 0,

                                status:
                                    'deleted'

                            });

                        }


                        /* ITEM */

                        else {

                            deletedItems.push({

                                sale_detail_id:
                                data.sale_detail_id,

                                itemcategory_id:
                                data.itemcategory_id,

                                no_seri:
                                data.code,

                                code:
                                data.code,

                                name:
                                data.name,

                                price:
                                    parseFloat(
                                        data.price_sale ??
                                        data.price ??
                                        0
                                    ) || 0,

                                price_sale:
                                    parseFloat(
                                        data.price_sale ??
                                        data.price ??
                                        0
                                    ) || 0,

                                qty:
                                    1,

                                status:
                                    'deleted'

                            });

                        }


                        table
                            .row(row)
                            .remove()
                            .draw(false);


                        calculateTotal();

                    });

                }
            );


            /* =========================================================
               SIMPAN TRANSAKSI
            ========================================================= */

            $('.btn-simpan').click(function () {

                let accessories = [];

                let items = [];


                /* =====================================================
                   LOOP DATATABLE
                ====================================================== */

                table.rows().every(function () {

                    let rowApi =
                        this;

                    let data =
                        rowApi.data();

                    let node =
                        rowApi.node();


                    if (!node || !data) {
                        return;
                    }


                    if (
                        data.status ===
                        'return'
                    ) {

                        return;

                    }


                    let qty =
                        parseInt(
                            $(node)
                                .find('.qty-input')
                                .val()
                        ) || 0;


                    /* ACCESSORIES */

                    if (
                        data.type ===
                        'accessory'
                    ) {

                        let priceSale =
                            rupiahToNumber(
                                $(node)
                                    .find('.price-input')
                                    .val()
                            );


                        accessories.push({

                            sale_detail_id:
                                data.sale_detail_id ??
                                null,

                            accessories_id:
                            data.accessories_id,

                            qty:
                            qty,

                            price_sale:
                            priceSale,

                            status:
                                data.status ??
                                'old'

                        });

                    }


                    /* ITEMS */

                    if (
                        data.type ===
                        'item'
                    ) {

                        let price =
                            rupiahToNumber(
                                $(node)
                                    .find('.price-input')
                                    .val()
                            );


                        items.push({

                            sale_detail_id:
                                data.sale_detail_id ??
                                null,

                            itemcategory_id:
                            data.itemcategory_id,

                            no_seri:
                            data.code,

                            name:
                            data.name,

                            price:
                            price,

                            price_sale:
                            price,

                            qty:
                                1,

                            status:
                                data.status ??
                                'old'

                        });

                    }

                });


                /* =====================================================
                   DELETED ACCESSORIES
                ====================================================== */

                deletedAccessories.forEach(
                    function (data) {

                        accessories.push({

                            sale_detail_id:
                            data.sale_detail_id,

                            accessories_id:
                            data.accessories_id,

                            qty:
                            data.qty,

                            price_sale:
                                data.price_sale ?? 0,

                            status:
                                'deleted'

                        });

                    }
                );


                /* =====================================================
                   DELETED ITEMS
                ====================================================== */

                deletedItems.forEach(
                    function (data) {

                        items.push({

                            sale_detail_id:
                            data.sale_detail_id,

                            itemcategory_id:
                            data.itemcategory_id,

                            no_seri:
                            data.no_seri,

                            name:
                            data.name,

                            price:
                                data.price ?? 0,

                            price_sale:
                                data.price_sale ??
                                data.price ??
                                0,

                            qty:
                                1,

                            status:
                                'deleted'

                        });

                    }
                );


                /* =====================================================
                   VALIDASI
                ====================================================== */

                if (
                    accessories.length === 0 &&
                    items.length === 0
                ) {

                    Swal.fire(
                        'Peringatan',
                        'Tidak ada barang dalam transaksi.',
                        'warning'
                    );

                    return;

                }


                /* =====================================================
                   PAYMENT METHOD
                ====================================================== */

                let paymentMethod =
                    $('input[name="payment_method"]:checked')
                        .val();


                let bankId = null;

                let penerima = null;


                if (
                    paymentMethod ===
                    'transfer'
                ) {

                    bankId =
                        $('#bank').val() || null;

                    penerima =
                        $('#penerima').val() || null;


                    /*
                     * Validasi bank
                     */

                    if (!bankId) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Bank Belum Dipilih',

                            text:
                                'Silakan pilih bank terlebih dahulu.'

                        });

                        return;

                    }


                    /*
                     * Validasi penerima
                     */

                    if (!penerima) {

                        Swal.fire({

                            icon:
                                'warning',

                            title:
                                'Nama Penerima Kosong',

                            text:
                                'Silakan masukkan nama penerima.'

                        });

                        return;

                    }

                }


                /* =====================================================
                   PRICE SALE
                ====================================================== */

                let priceSaleForController = 0;


                let newAccessory =
                    accessories.find(function (row) {

                        return (
                            row.status ===
                            'new'
                        );

                    });


                if (newAccessory) {

                    priceSaleForController =
                        parseFloat(
                            newAccessory.price_sale ?? 0
                        ) || 0;

                }


                if (
                    priceSaleForController <= 0
                ) {

                    let firstProduct =
                        accessories.find(function (row) {

                            return (
                                row.status !==
                                'deleted'
                            );

                        });


                    if (firstProduct) {

                        priceSaleForController =
                            parseFloat(
                                firstProduct.price_sale ?? 0
                            ) || 0;

                    }

                }


                /*
                 * Jika accessories tidak ada,
                 * ambil dari item.
                 */

                if (
                    priceSaleForController <= 0
                ) {

                    let firstItem =
                        items.find(function (row) {

                            return (
                                row.status !==
                                'deleted'
                            );

                        });


                    if (firstItem) {

                        priceSaleForController =
                            parseFloat(
                                firstItem.price_sale ??
                                firstItem.price ??
                                0
                            ) || 0;

                    }

                }


                /* =====================================================
                   AJAX UPDATE
                ====================================================== */

                $.ajax({

                    url:
                        '{{ route("manager.sale.update", $sale->id) }}',

                    method:
                        'PUT',

                    data: {

                        _token: '{{ csrf_token() }}',

                        customer_id:
                            $('#customer_id').val(),

                        divisi_id:
                            $('#single-select-optgroup-field').val(),

                        total_item:
                            $('#total_item').val(),

                        total_price:
                            rupiahToNumber(
                                $('#totalrp').val()
                            ),

                        no_po:
                            $('#no_po').val(),

                        created_at:
                            $('#created_at').val(),

                        deadlines:
                            $('#deadlines').val(),

                        description:
                            $('#payment_cash').val(),

                        payment_method:
                        paymentMethod,

                        bank_id:
                        bankId,

                        penerima:
                        penerima,

                        date_pay:
                            $('#date_pay').val(),

                        diskon:
                            rupiahToNumber(
                                $('#diskon').val()
                            ),

                        ongkir:
                            rupiahToNumber(
                                $('#ongkir').val()
                            ),

                        ppn:
                            rupiahToNumber(
                                $('#ppn').val()
                            ),

                        pph:
                            rupiahToNumber(
                                $('#pph').val()
                            ),

                        admin_fee:
                            rupiahToNumber(
                                $('#admin_fee').val()
                            ),

                        fee:
                            rupiahToNumber(
                                $('#fee').val()
                            ),

                        bayar:
                            rupiahToNumber(
                                $('#bayarrp').val()
                            ),

                        nominal_in:
                            rupiahToNumber(
                                $('#nominal_in').val()
                            ),

                        price_sale:
                        priceSaleForController,

                        accessories:
                        accessories,

                        items:
                        items
                    },


                    /* =================================================
                       BEFORE SEND
                    ================================================== */

                    beforeSend: function () {

                        $('.btn-simpan')
                            .prop(
                                'disabled',
                                true
                            )
                            .html(
                                '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...'
                            );

                    },


                    /* =================================================
                       SUCCESS
                    ================================================== */

                    success: function (response) {

                        if (
                            response.status ===
                            'success'
                        ) {

                            Swal.fire({

                                icon:
                                    'success',

                                title:
                                    'Berhasil',

                                text:
                                response.message,

                                timer:
                                    1500,

                                showConfirmButton:
                                    false

                            });


                            setTimeout(
                                function () {

                                    location.reload();

                                },
                                1500
                            );

                        } else {

                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );

                        }

                    },


                    /* =================================================
                       ERROR
                    ================================================== */

                    error: function (xhr) {

                        console.error(
                            xhr.responseJSON
                        );


                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan',
                            'error'
                        );

                    },


                    /* =================================================
                       COMPLETE
                    ================================================== */

                    complete: function () {

                        $('.btn-simpan')
                            .prop(
                                'disabled',
                                false
                            )
                            .html(
                                '<i class="bx bx-check-circle"></i> Simpan Perubahan'
                            );

                    }

                });

            });

        });

    </script>

@endpush
