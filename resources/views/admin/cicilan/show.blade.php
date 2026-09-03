@extends('layouts.master')

@section('title', 'DETAIL CICILAN')

@section('content')

    <div class="row">

        {{-- =========================================================
        | KONTEN KIRI
        ========================================================= --}}
        <div class="col-lg-8">

            {{-- =====================================================
            | DETAIL TRANSAKSI
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1 fw-bold">Detail Transaksi</h5>
                            <small class="text-muted">
                                Informasi transaksi penjualan
                            </small>
                        </div>

                        <a href="{{ route('admin.cicilan.index') }}"
                           class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">
                                Invoice
                            </small>

                            <strong>
                                {{ $sale->invoice ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">
                                Invoice Manual
                            </small>

                            <strong>
                                {{ $sale->inv_manual ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">
                                Tanggal Transaksi
                            </small>

                            <strong>
                                {{ $sale->created_at ? $sale->created_at->format('d-m-Y H:i') : '-' }}
                            </strong>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">
                                Customer
                            </small>

                            <strong>
                                {{ $sale->customer->name ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">
                                No. Telepon
                            </small>

                            <strong>
                                {{ $sale->customer->phone_wa ?? $sale->customer->phone ?? '-' }}
                            </strong>
                        </div>

                    </div>

                </div>
            </div>


            {{-- =====================================================
            | ITEM
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15 mt-3">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        <i class="bx bx-package me-1"></i>
                        Item
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle mb-0">

                            <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Item</th>
                                <th>No. Seri</th>
                                <th class="text-end">Harga</th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($sale->itemSales as $index => $item)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        {{ $item->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->no_seri ?? '-' }}
                                    </td>

                                    <td class="text-end">
                                        Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted">
                                        Tidak ada item.
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =====================================================
            | ACCESSORIES
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15 mt-3">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        <i class="bx bx-cube me-1"></i>
                        Accessories
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle mb-0">

                            <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Accessories</th>
                                <th width="100" class="text-center">
                                    Qty
                                </th>
                                <th class="text-end">
                                    Subtotal
                                </th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($sale->accessoriesSales as $index => $accessory)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        {{ $accessory->accessories->name ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $accessory->qty ?? 0 }}
                                    </td>

                                    <td class="text-end">
                                        Rp {{ number_format($accessory->subtotal ?? 0, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted">
                                        Tidak ada accessories.
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =====================================================
            | PENYESUAIAN TRANSAKSI
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15 mt-3">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="fw-bold mb-1">
                                <i class="bx bx-edit me-1"></i>
                                Penyesuaian Transaksi
                            </h5>

                            <small class="text-muted">
                                Ubah diskon, biaya admin, dan fee transaksi.
                            </small>

                        </div>

                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPenyesuaian">

                            <i class="bx bx-edit me-1"></i>
                            Penyesuaian

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
        | KONTEN KANAN
        ========================================================= --}}
        <div class="col-lg-4">


            {{-- =====================================================
            | RINGKASAN PEMBAYARAN
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="fw-bold mb-0">
                            Ringkasan Pembayaran
                        </h5>



                    </div>


                    {{-- TOTAL HARGA --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Total Harga
                    </span>

                        <strong>
                            Rp {{ number_format($sale->total_price ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- PPN --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        PPN
                    </span>

                        <strong class="">
                             Rp {{ number_format($sale->ppn ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>

                    {{-- PPH --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        PPN
                    </span>

                        <strong class="text-danger">
                            - Rp {{ number_format($sale->pph ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>

                    {{-- DISKON --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Diskon
                    </span>

                        <strong class="text-danger">
                            - Rp {{ number_format($sale->diskon ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- BIAYA ADMIN --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Biaya Admin
                    </span>

                        <strong class="text-danger">
                            - Rp {{ number_format($sale->admin_fee ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>
                    {{-- Ongkir --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Ongkir
                    </span>

                        <strong class="">
                             Rp {{ number_format($sale->ongkir ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- FEE --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Fee
                    </span>

                        <strong>
                            Rp {{ number_format($sale->fee ?? 0, 0, ',', '.') }}
                        </strong>

                    </div>


                    <hr>


                    {{-- TOTAL TAGIHAN --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="fw-bold">
                        Total Tagihan
                    </span>

                        <strong class="text-primary fs-5">
                            Rp {{ number_format($totalInvoice, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- SUDAH DIBAYAR --}}
                    <div class="d-flex justify-content-between mb-2">

                    <span class="text-muted">
                        Sudah Dibayar
                    </span>

                        <strong class="text-success">
                            Rp {{ number_format($totalBayar, 0, ',', '.') }}
                        </strong>

                    </div>


                    {{-- SISA PIUTANG --}}
                    <div class="d-flex justify-content-between">

                    <span class="fw-bold">
                        Sisa Piutang
                    </span>

                        <strong class="{{ $sisaPiutang > 0 ? 'text-danger' : 'text-success' }} fs-5">

                            Rp {{ number_format($sisaPiutang, 0, ',', '.') }}

                        </strong>

                    </div>
                    @if($sisaPiutang > 0)

                        <div class="text-center">
                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBayar">

                                <i class="bx bx-money me-1"></i>
                                Bayar Cicilan

                            </button>
                        </div>

                    @else

                        <div class="text-center">
                            <span class="badge bg-success">
                            LUNAS
                        </span>
                        </div>

                    @endif
                </div>

            </div>


            {{-- =====================================================
            | RIWAYAT PEMBAYARAN
            ===================================================== --}}
            <div class="card border-0 shadow-sm radius-15 mt-3">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        <i class="bx bx-history me-1"></i>
                        Riwayat Pembayaran
                    </h5>

                    @forelse($sale->debt as $debt)

                        <div class="border rounded p-3 mb-2">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    Rp {{ number_format($debt->pay_debts ?? 0, 0, ',', '.') }}
                                </strong>

                                <span class="badge bg-success">
                                Pembayaran
                            </span>

                            </div>

                            <div class="small text-muted mt-2">

                                <div>
                                    <strong>Tanggal:</strong>
                                    {{ $debt->date_pay ? \Carbon\Carbon::parse($debt->date_pay)->format('d-m-Y') : '-' }}
                                </div>

                                <div>
                                    <strong>Metode:</strong>

                                    @if($debt->bank)
                                        {{ $debt->bank->name }}
                                    @else
                                        {{ $debt->description ?? 'Cash' }}
                                    @endif
                                </div>

                                <div>
                                    <strong>Penerima:</strong>
                                    {{ $debt->penerima ?? '-' }}
                                </div>

                            </div>


                            <div class="text-end mt-2">

                                <form action="{{ route('admin.cicilan.payment.destroy', $debt->id) }}"
                                      method="POST"
                                      class="d-inline form-hapus-pembayaran">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">

                                        <i class="bx bx-trash"></i>
                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </div>

                    @empty

                        <div class="text-center text-muted py-3">
                            Belum ada pembayaran.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    | MODAL PENYESUAIAN TRANSAKSI
    ========================================================= --}}
    <div class="modal fade"
         id="modalPenyesuaian"
         tabindex="-1"
         aria-labelledby="modalPenyesuaianLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 radius-15">

                <div class="modal-header">

                    <h5 class="modal-title fw-bold"
                        id="modalPenyesuaianLabel">

                        <i class="bx bx-edit me-1"></i>
                        Penyesuaian Transaksi

                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <form action="{{ route('admin.cicilan.updateSales', $sale->id) }}"
                      method="POST"
                      id="formEditSales">

                    @csrf
                    @method('PUT')


                    <div class="modal-body">

                        {{-- DISKON --}}
                        <div class="mb-3">

                            <label for="diskon"
                                   class="form-label fw-semibold">

                                Diskon

                            </label>

                            <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                                <input type="text"
                                       name="diskon"
                                       id="diskon"
                                       class="form-control rupiah-input"
                                       value="{{ number_format($sale->diskon ?? 0, 0, ',', '.') }}"
                                       autocomplete="off">

                            </div>

                        </div>


                        {{-- BIAYA ADMIN --}}
                        <div class="mb-3">

                            <label for="admin_fee"
                                   class="form-label fw-semibold">

                                Biaya Admin

                            </label>

                            <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                                <input type="text"
                                       name="admin_fee"
                                       id="admin_fee"
                                       class="form-control rupiah-input"
                                       value="{{ number_format($sale->admin_fee ?? 0, 0, ',', '.') }}"
                                       autocomplete="off">

                            </div>

                        </div>


                        {{-- FEE --}}
                        <div class="mb-3">

                            <label for="fee"
                                   class="form-label fw-semibold">

                                Fee

                            </label>

                            <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                                <input type="text"
                                       name="fee"
                                       id="fee"
                                       class="form-control rupiah-input"
                                       value="{{ number_format($sale->fee ?? 0, 0, ',', '.') }}"
                                       autocomplete="off">

                            </div>

                        </div>


                        <hr>


                        {{-- TOTAL HARGA --}}
                        <div class="d-flex justify-content-between mb-2">

                        <span>
                            Total Harga
                        </span>

                            <strong>
                                Rp {{ number_format($sale->total_price ?? 0, 0, ',', '.') }}
                            </strong>

                        </div>


                        {{-- PREVIEW TAGIHAN --}}
                        <div class="d-flex justify-content-between">

                        <span class="fw-bold">
                            Total Tagihan
                        </span>

                            <strong class="text-primary"
                                    id="previewPay">

                                Rp {{ number_format($sale->pay ?? 0, 0, ',', '.') }}

                            </strong>

                        </div>


                        <div class="alert alert-info mt-3 mb-0">

                            <i class="bx bx-info-circle me-1"></i>

                            <small>
                                Diskon dan biaya admin mengurangi total tagihan.
                                Fee tidak mengurangi total tagihan.
                            </small>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bx bx-save me-1"></i>
                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
    | MODAL BAYAR CICILAN
    ========================================================= --}}
    <div class="modal fade"
         id="modalBayar"
         tabindex="-1"
         aria-labelledby="modalBayarLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 radius-15">

                <div class="modal-header">

                    <h5 class="modal-title fw-bold"
                        id="modalBayarLabel">

                        <i class="bx bx-money me-1"></i>
                        Bayar Cicilan

                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <form action="{{ route('admin.cicilan.payment', $sale->id) }}"
                      method="POST"
                      id="formPembayaran">

                    @csrf


                    <div class="modal-body">

                        {{-- SISA PIUTANG --}}
                        <div class="alert alert-warning">

                            <div class="d-flex justify-content-between">

                            <span>
                                Sisa Piutang
                            </span>

                                <strong>
                                    Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                                </strong>

                            </div>

                        </div>


                        {{-- TANGGAL --}}
                        <div class="mb-3">

                            <label for="date_pay"
                                   class="form-label fw-semibold">

                                Tanggal Pembayaran

                            </label>

                            <input type="date"
                                   name="date_pay"
                                   id="date_pay"
                                   class="form-control"
                                   value="{{ old('date_pay', date('Y-m-d')) }}"
                                   required>

                        </div>


                        {{-- NOMINAL --}}
                        <div class="mb-3">

                            <label for="pay_debts"
                                   class="form-label fw-semibold">

                                Nominal Pembayaran

                            </label>

                            <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                                <input type="text"
                                       name="pay_debts"
                                       id="pay_debts"
                                       class="form-control rupiah-input"
                                       placeholder="0"
                                       autocomplete="off"
                                       required>

                            </div>

                            <small class="text-muted">
                                Maksimal Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                            </small>

                        </div>


                        {{-- METODE PEMBAYARAN --}}
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Metode Pembayaran
                            </label>

                            <div class="row">

                                <div class="col-6">

                                    <div class="form-check">

                                        <input class="form-check-input"
                                               type="radio"
                                               name="payment_method"
                                               id="payment_cash"
                                               value="cash"
                                               checked>

                                        <label class="form-check-label"
                                               for="payment_cash">

                                            Cash

                                        </label>

                                    </div>

                                </div>


                                <div class="col-6">

                                    <div class="form-check">

                                        <input class="form-check-input"
                                               type="radio"
                                               name="payment_method"
                                               id="payment_transfer"
                                               value="transfer">

                                        <label class="form-check-label"
                                               for="payment_transfer">

                                            Transfer

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- DESCRIPTION --}}
                        <input type="hidden"
                               name="description"
                               id="description"
                               value="Cash">


                        {{-- DATA TRANSFER --}}
                        <div id="transferFields"
                             style="display: none;">

                            {{-- BANK --}}
                            <div class="mb-3">

                                <label for="bank_id"
                                       class="form-label fw-semibold">

                                    Bank

                                </label>

                                <select name="bank_id"
                                        id="bank_id"
                                        class="form-control">

                                    <option value="">
                                        -- Pilih Bank --
                                    </option>

                                    @foreach($banks as $bank)

                                        <option value="{{ $bank->id }}">

                                            {{ $bank->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- PENERIMA --}}
                            <div class="mb-3">

                                <label for="penerima"
                                       class="form-label fw-semibold">

                                    Penerima

                                </label>

                                <input type="text"
                                       name="penerima"
                                       id="penerima"
                                       class="form-control"
                                       placeholder="Nama penerima">

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                                class="btn btn-success">

                            <i class="bx bx-save me-1"></i>
                            Simpan Pembayaran

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    @push('head')

        <style>

            .radius-15 {
                border-radius: 15px !important;
            }

            .rupiah-input {
                text-align: right;
                font-weight: 500;
            }

            #transferFields {
                transition: all .2s ease-in-out;
            }

            .select2-container--open {
                z-index: 999999 !important;
            }

            .select2-dropdown {
                z-index: 999999 !important;
            }

        </style>

    @endpush


    @push('js')

        <script>

            $(document).ready(function () {

                /*
                |--------------------------------------------------------------------------
                | FORMAT RUPIAH
                |--------------------------------------------------------------------------
                */

                function formatRupiah(value) {

                    value = String(value || '').replace(/\D/g, '');

                    if (!value) {
                        return '';
                    }

                    return new Intl.NumberFormat('id-ID').format(
                        parseInt(value, 10)
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | UNFORMAT RUPIAH
                |--------------------------------------------------------------------------
                */

                function unformatRupiah(value) {

                    return String(value || '')
                        .replace(/\D/g, '');

                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE PREVIEW PAY
                |--------------------------------------------------------------------------
                */

                function updatePayPreview() {

                    let totalPrice = parseInt(
                        '{{ (int) ($sale->total_price ?? 0) }}',
                        10
                    ) || 0;


                    let diskon = parseInt(
                        unformatRupiah($('#diskon').val()),
                        10
                    ) || 0;


                    let biayaAdmin = parseInt(
                        unformatRupiah($('#admin_fee').val()),
                        10
                    ) || 0;


                    let pay = Math.max(
                        0,
                        totalPrice - diskon - biayaAdmin
                    );


                    $('#previewPay').text(
                        'Rp ' +
                        new Intl.NumberFormat('id-ID').format(pay)
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | FORMAT INPUT RUPIAH
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'input',
                    '#formEditSales .rupiah-input, #formPembayaran .rupiah-input',
                    function () {

                        let value = unformatRupiah(
                            $(this).val()
                        );

                        $(this).val(
                            formatRupiah(value)
                        );


                        if (
                            $(this).attr('id') === 'diskon' ||
                            $(this).attr('id') === 'admin_fee'
                        ) {

                            updatePayPreview();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MODAL PENYESUAIAN DIBUKA
                |--------------------------------------------------------------------------
                */

                $('#modalPenyesuaian').on(
                    'shown.bs.modal',
                    function () {

                        updatePayPreview();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SUBMIT PENYESUAIAN
                |--------------------------------------------------------------------------
                */

                $('#formEditSales').on(
                    'submit',
                    function () {

                        $(this)
                            .find('.rupiah-input')
                            .each(function () {

                                $(this).val(
                                    unformatRupiah(
                                        $(this).val()
                                    )
                                );

                            });

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SELECT2 BANK
                |--------------------------------------------------------------------------
                */

                $('#bank_id').select2({

                    width: '100%',

                    placeholder: '-- Pilih Bank --',

                    allowClear: true,

                    dropdownParent: $('#modalBayar')

                });


                /*
                |--------------------------------------------------------------------------
                | METODE PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                $('input[name="payment_method"]').on(
                    'change',
                    function () {

                        let method = $(this).val();


                        if (method === 'transfer') {

                            $('#transferFields').slideDown();

                            $('#description').val(
                                'Transfer Bank'
                            );

                            $('#bank_id').prop(
                                'required',
                                true
                            );

                            $('#penerima').prop(
                                'required',
                                true
                            );

                        } else {

                            $('#transferFields').slideUp();

                            $('#description').val(
                                'Cash'
                            );

                            $('#bank_id')
                                .val('')
                                .trigger('change')
                                .prop('required', false);

                            $('#penerima')
                                .val('')
                                .prop('required', false);

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MODAL BAYAR DIBUKA
                |--------------------------------------------------------------------------
                */

                $('#modalBayar').on(
                    'shown.bs.modal',
                    function () {

                        $('#pay_debts').trigger('focus');

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SUBMIT PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                $('#formPembayaran').on(
                    'submit',
                    function (e) {

                        let nominal = parseInt(
                            unformatRupiah(
                                $('#pay_debts').val()
                            ),
                            10
                        ) || 0;


                        let sisaPiutang = parseInt(
                            '{{ (int) $sisaPiutang }}',
                            10
                        ) || 0;


                        if (nominal <= 0) {

                            e.preventDefault();

                            alert(
                                'Nominal pembayaran harus lebih dari 0.'
                            );

                            return false;

                        }


                        if (nominal > sisaPiutang) {

                            e.preventDefault();

                            alert(
                                'Pembayaran tidak boleh melebihi sisa piutang.'
                            );

                            return false;

                        }


                        $('#pay_debts').val(
                            nominal
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | RESET MODAL PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                $('#modalBayar').on(
                    'hidden.bs.modal',
                    function () {

                        $('#formPembayaran')[0].reset();

                        $('#description').val(
                            'Cash'
                        );

                        $('#transferFields').hide();

                        $('#bank_id')
                            .val('')
                            .trigger('change');

                        $('#bank_id').prop(
                            'required',
                            false
                        );

                        $('#penerima').prop(
                            'required',
                            false
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | KONFIRMASI HAPUS PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                $('.form-hapus-pembayaran').on(
                    'submit',
                    function (e) {

                        if (
                            !confirm(
                                'Apakah Anda yakin ingin menghapus pembayaran ini?'
                            )
                        ) {

                            e.preventDefault();

                        }

                    }
                );

            });

        </script>

    @endpush

@endsection
