@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <div class="row">
                    <div class="col-sm-4">
                        <h4>New Transaction</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-end">
                            <select name="divisi_id" id="single-select-optgroup-field"
                                    data-placeholder="--Pilih Divisi--" class="form-control accessory-select">
                                @foreach($divisi as $div)
                                    @if(isset($sale))
                                        <option
                                            value="{{ $div->id }}" {{ $sale->divisi_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="float-end">
                            <input type="text" name="created_at" class="form-control datepicker" id="created_at"
                                   placeholder="Tanggal Invoice" value="{{$sale->created_at}}">
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div class="card-body">
            <form class="form-sale">
                @csrf
                <div class="row">
                    <div class="col-sm-9">
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
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group row">
                            <label for="code" class="col-lg-3 mt-2">
                                <strong>No PO:</strong>
                            </label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="no_po" id="no_po"
                                           value="{{$sale->no_po}}"
                                           placeholder="No PO">
                                </div>
                            </div>
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
                <tbody id="accessoriesTableBody">
                </tbody>
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
                                <input type="text" name="ppn" class="form-control" id="ppn" onkeyup="formatRupiah(this)" value="{{$sale->ppn}}">
                            </div>
                            <div class="mt-2">
                                <label for="">PPH</label>
                                <input type="text" class="form-control" name="pph" id="pph" onkeyup="formatRupiah(this)" value="{{$sale->pph}}">
                            </div>
                            <div class="mt-4">
                                <label for="">Penerima</label>
                                <input type="text" class="form-control" name="penerima" id="penerima" value="{{$sale->debt->first()->penerima ?? null}}">
                            </div>
                        </div>
                        <div class=" col-lg-4 float-end">
                            <div class="mt-2">
                                <label for="">Nominal In</label>
                                <input type="text" name="nominal_in" class="form-control" id="nominal_in"
                                       value="{{$sale->nominal_in}}" onkeyup="formatRupiah(this)">
                            </div>
                            <div class="mt-2">
                                <label for="">Pay Plan</label>
                                <input type="text" class="form-control datepicker" name="deadlines" id="deadlines"
                                       value="{{$sale->deadlines}}">
                            </div>
                            @php
                                $selectedBankId = $sale->debt->first()->bank_id ?? null;
                            @endphp

                            <div class="mt-4">
                                <label for="">Bank</label>
                                <select name="bank_id" id="bank"
                                        data-placeholder="--Pilih Bank--"
                                        class="form-control accessory-select">
                                    <option value=""></option>
                                    @foreach($bank as $data)
                                        <option value="{{ $data->id }}" {{ $selectedBankId == $data->id ? 'selected' : '' }}>
                                            {{ $data->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-2">
                                <label for="">Lainya</label>
                                <textarea class="form-control datepicker" name="description" id="description">{{$sale->debt->first()->description ?? null}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <input type="hidden" name="total_item" id="total_item" readonly>
                        <div class="form-group row mb-2">
                            <label for="customer" class="col-lg-4 control-label">Customer</label>
                            <div class="col-lg-8">
                                <select name="customer_id" id="single-select-field"
                                        data-placeholder="--Pilih Customer--"
                                        class="form-control accessory-select">
                                    <option value=""></option>
                                    @foreach($customers as $customer)
                                        <option
                                            value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="totalrp" class="col-lg-4 control-label">Total Price</label>
                            <div class="col-lg-8">
                                <input type="text" id="totalrp" class="form-control" readonly
                                       value="{{formatRupiah($sale->total_price)}}">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="diskon" class="col-lg-4 control-label">Diskon</label>
                            <div class="col-lg-8">
                                <input type="number" name="diskon" id="diskon" class="form-control" value="{{$sale->diskon}}">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="ongkir" class="col-lg-4 control-label">Ongkir</label>
                            <div class="col-lg-8">
                                <input type="number" name="ongkir" id="ongkir" class="form-control" value="{{$sale->ongkir}}">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="bayar" class="col-lg-4 control-label">Bayar</label>
                            <div class="col-lg-8">
                                <input type="text" id="bayarrp" name="bayar" class="form-control" readonly
                                       value="{{formatRupiah($sale->pay)}}">
                            </div>
                        </div>
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
            $('#invoice').select2({
                theme: 'bootstrap-5',
                placeholder: "--Pilih Invoice--",
                width: '100%'
            });
        });
        $(document).ready(function () {
            $('#bank').select2({
                theme:'bootstrap-5',
                placeholder: "--Pilih Bank--",
                width: '100%'
            });
        });
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
                        data: 'qty', render: function (data, type, row) {
                            return '<input type="number" name="qty[]" class="form-control stok-input" value="' + data + '"/>';
                        }
                    },
                    {
                        data: null, searchable: false, sortable: false, render: function (data, type, row) {
                            return `<button class="btn btn-danger btn-delete bx bx-trash" data-id="${row.id}"></button>`;
                        }
                    }
                ],
                dom: 'Brt',
                bSort: false,
                paginate: false,
                lengthChange: false,
                buttons: []
            });

            let data1 = @json($sale->accessoriesSales);
            data1.forEach(item => {
                table.row.add({
                    code: item.accessories.code_acces,
                    name: item.accessories.name,
                    price: item.accessories.price,
                    accessories_id: item.accessories_id,
                    qty: item.qty,
                    id: item.id // pastikan menggunakan item.id untuk aksesori
                }).draw();
            });

            let data2 = @json($sale->itemSales);
            data2.forEach(item => {
                table.row.add({
                    code: item.no_seri,
                    name: item.name,
                    price: item.price,
                    qty: 1, // Misalnya hanya ada 1 per item no_seri
                    id: item.id // pastikan menggunakan item.id untuk item
                }).draw();
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

                let ongkir = parseFloat($('#ongkir').val().replace(/[^0-9,-]/g, "").replace(',', '.'));
                if (isNaN(ongkir)) {
                    ongkir = 0;
                }
                bayar += ongkir;

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

                $('#totalrp').val('Rp. ' + Math.floor(total).toLocaleString('id-ID'));
                $('#bayarrp').val('Rp. ' + Math.floor(bayar).toLocaleString('id-ID'));
                $('#nominal_in').val('' + Math.floor(bayar));
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
                            url: '{{ route('manager.sale.checkcode') }}',
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
                                            data.qty += 1;
                                            this.data(data).draw();
                                            found = true;
                                            return false;
                                        } else if (response.type === 'item' && data.no_seri === response.data.no_seri) {
                                            data.qty += 1;
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
                                                qty: 1,
                                                accessories_id: response.data.id
                                            }).draw();
                                        } else if (response.type === 'item') {
                                            table.row.add({
                                                code: response.data.no_seri,
                                                name: response.data.name,
                                                price: response.data.price,
                                                qty: 1,
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
                let sale_id = '{{ $sale->id }}';

                table.rows().every(function () {
                    let data = this.data();
                    if (data) {
                        let qty = parseInt($(this.node()).find('input.stok-input').val());

                        if (data.accessories_id && qty > 0) {
                            accessoriesData.push({
                                id: data.id,
                                sale_id: sale_id,
                                accessories_id: data.accessories_id,
                                qty: qty,
                                subtotal: parseFloat(data.price.replace(/[^0-9,-]/g, "").replace(',', '.')) * qty
                            });
                        } else if (data.itemcategory_id) {
                            itemsData.push({
                                id: data.id,
                                sale_id: sale_id,
                                itemcategory_id: data.itemcategory_id,
                                name: data.name,
                                no_seri: data.code,
                                price: parseFloat(data.price.replace(/[^0-9,-]/g, "").replace(',', '.')),
                            });
                        }
                    }

                });

                $.ajax({
                    url: '{{route('manager.sale.edit', $sale->id)}}',
                    method: 'GET',
                    success: function (data) {
                        $('#totalrp').val(data.total_price);
                        $('#diskon').val(data.diskon);
                        $('#ongkir').val(data.ongkir);
                        $('#bayarrp').val(data.pay);
                        $('#nominal_in').val(data.nominal_in);
                        $('#no_po').val(data.no_po);
                        $('#ppn').val(data.ppn);
                        $('#pph').val(data.pph);
                        $('#created_at').val(data.created_at);
                        $('#deadlines').val(data.deadlines);
                    }
                });

                $.ajax({
                    url: '{{ route('manager.sale.update', $sale->id) }}',
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: $('select[name="customer_id"]').val(),
                        divisi_id: $('select[name="divisi_id"]').val(),
                        bank_id: $('select[name="bank_id"]').val(),
                        total_item: $('#total_item').val(),
                        total_price: Math.floor(parseFloat($('#totalrp').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        ongkir: $('#ongkir').val(),
                        diskon: $('#diskon').val(),
                        nominal_in: $('#nominal_in').val(),
                        deadlines: $('#deadlines').val(),
                        no_po: $('#no_po').val(),
                        ppn: $('#ppn').val(),
                        pph: $('#pph').val(),
                        penerima: $('#penerima').val(),
                        description: $('#description').val(),
                        created_at: $('#created_at').val(),
                        bayar: Math.floor(parseFloat($('#bayarrp').val().replace(/[^0-9,-]/g, "").replace(',', '.'))),
                        accessories: accessoriesData,
                        items: itemsData,

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
    <script>
        $(document).on('click', '.btn-delete', function () {
            let itemId = $(this).data('id'); // Gunakan ID yang benar dari itemSale atau accessorySale
            Swal.fire({
                title: 'Anda yakin?',
                text: "Data akan dihapus dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('manager.sale.destroy', ':id') }}`.replace(':id', itemId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Dihapus!',
                                    response.message,
                                    'success'
                                );
                                // Reload atau update tampilan setelah delete
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

    </script>
@endpush
