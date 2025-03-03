@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>New Transaction</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-end">
                            <div class="form-group row">
                                <label for="code" class="mt-2">
                                    <strong>No PO</strong>
                                </label>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="no_po" id="no_po"
                                               placeholder="Nomor PO">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-sale">
                @csrf
                <div class="form-group row">
                    <label for="code" class="col-lg-2 mt-2">
                        <strong>Kode:</strong>
                    </label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" id="code"
                                   placeholder="Enter Code or Serial">
                        </div>
                    </div>
                </div>
            </form>
            <table class="table mt-2 table-sale">
                <thead>
                <tr>
                    <th scope="col" width="12%">Code/No Seri</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody id="accessoriesTableBody"></tbody>
            </table>
            <form action="" class="form-pembelian" method="post">
                @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="bg-primary">
                        <h1 class="text-center" id="totalAmount">Bayar Rp. 0</h1>
                    </div>
                    <div class=" col-lg-4 float-end ms-2">
                        <div class="mt-2">
                            <label for="">PPN</label>
                            <input type="text" name="ppn" class="form-control" id="ppn" onkeyup="formatRupiah(this)" value="0">
                        </div>
                        <div class="mt-2">
                            <label for="">PPH</label>
                            <input type="text" class="form-control" name="pph" id="pph" onkeyup="formatRupiah(this)" value="0">
                        </div>
                    </div>
                    <div class=" col-lg-4 float-end">
                        <div class="mt-2">
                            <label for="">Nominal In</label>
                            <input type="text" name="nominal_in" class="form-control" id="nominal_in" onkeyup="formatRupiah(this)">
                        </div>
                        <div class="mt-2">
                            <label for="">Pay Plan</label>
                            <input type="text" class="form-control datepicker" name="deadlines" id="deadlines">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                        <input type="hidden" name="total_item" id="total_item" readonly>
                        <div class="form-group row mb-2">
                            <label for="customer" class="col-lg-4 control-label">Customer</label>
                            <div class="col-lg-8">
                                <select name="customer_id" id="single-select-field"
                                        data-placeholder="--Pilih Customer--" class="form-control accessory-select">
                                    <option value=""></option>
                                    @foreach($customer as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="totalrp" class="col-lg-4 control-label">Total Price</label>
                            <div class="col-lg-8">
                                <input type="text" id="totalrp" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="diskon" class="col-lg-4 control-label">Diskon</label>
                            <div class="col-lg-8">
                                <input type="number" name="diskon" id="diskon" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="ongkir" class="col-lg-4 control-label">Ongkir Konsumen</label>
                            <div class="col-lg-8">
                                <input type="number" name="ongkir" id="ongkir" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="bayar" class="col-lg-4 control-label">Bayar</label>
                            <div class="col-lg-8">
                                <input type="text" id="bayarrp" name="bayar" class="form-control" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm float-end btn-simpan"><i class="bx bx-save"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/number-to-words"></script>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            let table = $('.table-sale').DataTable({
                processing: true,
                autoWidth: false,
                dataSrc: 'data',
                dataType: 'json',
                columns: [
                    {data: 'code'},
                    {data: 'name'},
                    {
                        data: 'price', render: function (data, type, row) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'stok', render: function (data, type, row) {
                            return '<input type="number" name="qty[]" class="form-control stok-input" value="' + data + '" />';
                        }
                    },
                    {
                        data: null, searchable: false, sortable: false, render: function (data, type, row) {
                            return '<button class="btn btn-danger btn-sm bx bx-trash" type="button"></button>';
                        }
                    }
                ],
                dom: 'Brt',
                bSort: false,
                paginate: false,
                lengthChange: false,
                buttons: []
            });

            function formatRupiah(amount) {
                let number_string = amount.toString().replace(/[^,\d]/g, '');
                let split = number_string.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return 'Rp ' + rupiah;
            }

            function calculateTotal() {
                let total = 0;
                let bayar = 0;
                let totalQty = 0;

                table.rows().every(function () {
                    let data = this.data();
                    let price = parseFloat(data.price.replace(/[^0-9,-]/g, "").replace(',', '.'));
                    let qty = parseInt($(this.node()).find('input.stok-input').val());

                    if (!isNaN(price) && !isNaN(qty) && qty > 0) {
                        total += price * qty;
                        totalQty += qty;
                    }
                });

                let diskon = parseFloat($('#diskon').val().replace(/[^0-9,-]/g, "").replace(',', '.'));
                if (isNaN(diskon)) {
                    diskon = 0;
                }
                bayar = total - diskon;

                let pph = parseFloat($('#pph').val().replace(/[^0-9,-]/g, "").replace(',', '.'));
                if (isNaN(pph)) {
                    pph = 0;
                }
                bayar -= pph;

                let ppn = parseFloat($('#ppn').val().replace(/[^0-9,-]/g, "").replace(',', '.'));
                if (isNaN(ppn)) {
                    ppn = 0;
                }
                bayar += ppn;

                let ongkir = parseFloat($('#ongkir').val().replace(/[^0-9,-]/g, "").replace(',', '.'));
                if (isNaN(ongkir)) {
                    ongkir = 0;
                }
                bayar += ongkir;

                $('#totalrp').val('Rp. ' + Math.floor(total).toLocaleString('id-ID'));
                $('#nominal_in').val('' + Math.floor(bayar));
                $('#bayarrp').val('Rp. ' + Math.floor(bayar).toLocaleString('id-ID'));
                $('#totalAmount').text('Bayar Rp. ' + Math.floor(bayar).toLocaleString('id-ID'));

                $('#total_item').val(totalQty);
            }

            $(document).on('input', '.stok-input', function () {
                let $input = $(this);
                let stokValue = parseInt($input.val());

                if (isNaN(stokValue) || stokValue < 0) {
                    $input.val(0);
                    stokValue = 0;
                }

                if (stokValue < 1) {
                    removeRow($input.closest('tr').find('.bx-trash')[0]);
                } else {
                    calculateTotal();
                }
            });

            $('#diskon').on('input', function () {
                calculateTotal();
            });

            $('#ongkir').on('input', function () {
                calculateTotal();
            });
            $('#ppn').on('input', function () {
                calculateTotal();
            });
            $('#pph').on('input', function () {
                calculateTotal();
            });

            $('#code').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let codeSale = $(this).val();
                    if (codeSale) {
                        $.ajax({
                            url: '{{ route('admin.sale.checkcode') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                code: codeSale
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    let found = false;
                                    table.rows().every(function () {
                                        let data = this.data();
                                        if (response.type === 'accessory' && data.code === response.data.code_acces) {
                                            data.stok += 1;
                                            this.data(data).draw();
                                            found = true;
                                            return false;
                                        } else if (response.type === 'item' && data.no_seri === response.data.no_seri) {
                                            data.stok += 1;
                                            this.data(data).draw();
                                            found = true;
                                            return false;
                                        }
                                    });

                                    if (!found) {
                                        if (response.type === 'accessory') {
                                            table.row.add({
                                                code: response.data.code_acces,
                                                name: response.data.name,
                                                price: response.data.price,
                                                stok: 1,
                                                accessories_id: response.data.id
                                            }).draw();
                                        } else if (response.type === 'item') {
                                            table.row.add({
                                                code: response.data.no_seri,
                                                name: response.data.name,
                                                price: response.data.price,
                                                stok: 1,
                                                itemcategory_id: response.data.itemcategory_id
                                            }).draw();
                                        }
                                    }
                                    calculateTotal();
                                    $('#code').val('');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message
                                    });
                                }
                            }
                        });
                    }
                }
            });

            $('.form-pembelian').on('submit', function (e) {
                e.preventDefault();

                let accessoriesData = [];
                let itemsData = [];

                table.rows().every(function () {
                    let data = this.data();
                    let qty = $(this.node()).find('input.stok-input').val();

                    if (data.code && data.name && qty) {
                        if (data.accessories_id) {
                            accessoriesData.push({
                                accessories_id: data.accessories_id,
                                qty: qty,
                                subtotal: parseFloat(data.price.replace(/[^0-9,-]/g, "").replace(',', '.')) * qty
                            });
                        } else if (data.itemcategory_id) {
                            itemsData.push({
                                itemcategory_id: data.itemcategory_id,
                                name: data.name,
                                price: parseFloat(data.price.replace(/[^0-9,-]/g, "").replace(',', '.')),
                                capital_price: data.capital_price,
                                no_seri: data.code,
                                date_in: data.created_at
                            });
                        }
                    }
                });

                $.ajax({
                    url: '{{ route('admin.sale.store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: $('select[name="customer_id"]').val(),
                        total_item: $('#total_item').val(),
                        total_price: Math.floor(parseFloat($('#totalrp').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        ongkir: $('#ongkir').val(),
                        diskon: $('#diskon').val(),
                        nominal_in: Math.floor(parseFloat($('#nominal_in').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        ppn: Math.floor(parseFloat($('#ppn').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        pph: Math.floor(parseFloat($('#pph').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        deadlines: $('#deadlines').val(),
                        no_po: $('#no_po').val(),
                        bayar: Math.floor(parseFloat($('#bayarrp').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        accessories: accessoriesData,
                        items: itemsData
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = 'Stok Tidak Mencukupi';
                        if (errors) {
                            errorMessage = Object.values(errors).join(' ');
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            });

            $('.btn-simpan').on('click', function () {
                $('.form-pembelian').submit();
            });

            $(document).on('click', '.bx-trash', function () {
                removeRow(this);
            });

            function removeRow(button) {
                let row = $(button).closest('tr');
                table.row(row).remove().draw();
                calculateTotal();
            }
        });
    </script>

@endpush


