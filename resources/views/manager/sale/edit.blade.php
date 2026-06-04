@extends('layouts.master')

@section('content')

    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <div class="row">
                    <div class="col-sm-4">
                        <h4>Edit Transaction</h4>
                        <h4>{{$sale->invoice}}</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-end">
                            <select name="divisi_id"
                                    id="single-select-optgroup-field"
                                    data-placeholder="--Pilih Divisi--"
                                    class="form-control accessory-select">
                                @foreach($divisi as $div)
                                    <option value="{{ $div->id }}"
                                        {{ $sale->divisi_id == $div->id ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="float-end">
                            <input type="text" name="created_at" class="form-control datepicker" id="created_at"
                                   value="{{$sale->created_at}}">
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="card-body">
            {{-- INPUT BARANG --}}
            <div class="row">
                <div class="col-sm-9">
                    <div class="form-group row">
                        <label class="col-lg-2 mt-2">
                            <strong>Kode:</strong>
                        </label>
                        <div class="col-lg-5">
                            <input type="text"
                                   class="form-control"
                                   id="code"
                                   placeholder="Enter Code or Serial">
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group row">
                        <label class="col-lg-3 mt-2">
                            <strong>No PO:</strong>
                        </label>
                        <div class="col-lg-7">
                            <input type="text" class="form-control" id="no_po" value="{{$sale->no_po}}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <table class="table table-sale mt-3">
                <thead>
                <tr>
                    <th width="15%">Code</th>
                    <th>Name</th>
                    <th width="15%">Price</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Status</th>
                    <th width="10%">Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>

            <form class="form-pembelian">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="bg-primary">
                            <h1 class="text-center text-white p-2" id="totalAmount">
                                Bayar Rp. 0
                            </h1>
                        </div>

                        <div class="col-lg-4 float-end ms-2">
                            <div class="mt-2">
                                <label>PPN</label>
                                <input type="text" id="ppn" class="form-control"
                                       value="{{$sale->ppn}}">
                            </div>
                            <div class="mt-2">
                                <label>PPH</label>
                                <input type="text" id="pph" class="form-control" value="{{$sale->pph}}">
                            </div>
                            <div class="mt-4">
                                <label>Penerima</label>
                                <input type="text" id="penerima" class="form-control"
                                       value="{{$sale->debt->first()->penerima ?? null}}">
                            </div>
                        </div>

                        <div class="col-lg-4 float-end">
                            <div class="mt-2">
                                <label>Nominal In</label>
                                <input type="text" id="nominal_in" class="form-control" value="{{$sale->nominal_in}}">
                            </div>
                            <div class="mt-2">
                                <label>Pay Plan</label>
                                <input type="text" id="deadlines" class="form-control datepicker"
                                       value="{{$sale->deadlines}}">
                            </div>
                            @php
                                $selectedBankId = $sale->debt->first()->bank_id ?? null;
                            @endphp
                            <div class="mt-4">
                                <label>Bank</label>
                                <select id="bank" class="form-control accessory-select">
                                    <option value=""></option>
                                    @foreach($bank as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $selectedBankId == $data->id ? 'selected' : '' }}>
                                            {{ $data->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2">
                                <label>Lainya</label>
                                <textarea id="description"
                                          class="form-control">{{$sale->debt->first()->description ?? null}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <input type="hidden" id="total_item">
                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Customer</label>
                            <div class="col-lg-8">
                                <select id="customer_id" class="form-control accessory-select">
                                    <option value=""></option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Total
                            </label>
                            <div class="col-lg-8">
                                <input type="text" id="totalrp" readonly class="form-control"
                                       value="{{ formatRupiah($sale->total_price) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Diskon</label>
                            <div class="col-lg-8">
                                <input type="text" id="diskon" class="form-control" value="{{$sale->diskon}}" onkeyup="formatRupiah(this)">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label for="ongkir" class="col-lg-4 control-label">Fee</label>
                            <div class="col-lg-8">
                                <input type="text" name="fee" id="fee" class="form-control" value="{{$sale->fee}}" onkeyup="formatRupiah(this)" >
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Ongkir</label>
                            <div class="col-lg-8">
                                <input type="text" id="ongkir" class="form-control" value="{{$sale->ongkir}}" onkeyup="formatRupiah(this)">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Biaya Admin</label>
                            <div class="col-lg-8">
                                <input type="text" id="admin_fee" class="form-control" value="{{$sale->admin_fee}}" onkeyup="formatRupiah(this)">
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-lg-4 control-label">Bayar</label>
                            <div class="col-lg-8">
                                <input type="text" id="bayarrp" readonly class="form-control"
                                       value="{{ formatRupiah($sale->pay) }}">
                            </div>
                        </div>

                    </div>

                </div>

            </form>

        </div>

        <div class="card-footer">

            <button type="button"
                    class="btn btn-primary btn-sm float-end btn-simpan">

                <i class="bx bx-save"></i>
                Save

            </button>

        </div>

    </div>

@endsection

@push('head')

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

@endpush

@push('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        $(document).ready(function () {

            let table = $('.table-sale').DataTable({

                processing: true,
                searching: false,
                paging: false,
                info: false,
                ordering: false,

                columns: [

                    {data: 'code'},

                    {data: 'name'},

                    {
                        data: 'price',
                        render: function (data) {
                            return formatRupiah(data);
                        }
                    },

                    {
                        data: 'qty',
                        render: function (data) {

                            return `
                                <input type="number"
                                       class="form-control qty-input"
                                       min="1"
                                       value="${data}">
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
                        render: function (data, type, row) {

                            return `
                                <button type="button"
                                        class="btn btn-danger btn-delete"
                                        data-id="${row.sale_detail_id}"
                                        data-type="${row.type}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            `;
                        }
                    }

                ]

            });

            // ACCESSORIES
            // ACCESSORIES
            let accessories = @json($sale->accessoriesSales);

            accessories.forEach(item => {

                table.row.add({

                    code: item.accessories.code_acces,
                    name: item.accessories.name,
                    price: item.accessories.price,
                    qty: item.qty,

                    accessories_id: item.accessories_id,

                    sale_detail_id: item.id,

                    type: 'accessory',

                    status: item.status_return == 1
                        ? 'return'
                        : 'old'

                }).draw();

            });

            // ITEMS
            // ITEMS
            let items = @json($sale->itemSales);

            items.forEach(item => {

                table.row.add({

                    code: item.no_seri,
                    name: item.name,
                    price: item.price,
                    qty: 1,

                    itemcategory_id: item.itemcategory_id,

                    sale_detail_id: item.id,

                    type: 'item',

                    status: item.status_return == 1
                        ? 'return'
                        : 'old'

                }).draw();

            });

            calculateTotal();

            function rupiahToNumber(value) {

                return parseInt(
                    (value || '0').toString().replace(/[^0-9]/g, '')
                ) || 0;
            }

            function formatRupiah(value) {

                return 'Rp. ' + parseInt(value || 0).toLocaleString('id-ID');
            }

            function calculateTotal() {

                let total = 0;
                let totalQty = 0;

                table.rows().every(function () {

                    let data = this.data();

                    let qty = parseInt(
                        $(this.node()).find('.qty-input').val()
                    ) || 0;

                    total += qty * parseInt(data.price);

                    totalQty += qty;

                });

                let bayar = total;

                bayar -= rupiahToNumber($('#diskon').val());
                bayar += rupiahToNumber($('#ongkir').val());
                bayar += rupiahToNumber($('#ppn').val());
                bayar -= rupiahToNumber($('#pph').val());
                bayar -= rupiahToNumber($('#admin_fee').val());
                bayar -= rupiahToNumber($('#fee').val());

                $('#totalrp').val(formatRupiah(total));

                $('#bayarrp').val(formatRupiah(bayar));

                $('#total_item').val(totalQty);

                $('#nominal_in').val(bayar);

                $('#totalAmount').text(
                    'Bayar Rp. ' + bayar.toLocaleString('id-ID')
                );

            }

            $(document).on('input',
                '.qty-input,#diskon,#ongkir,#ppn,#pph,#admin_fee,#fee',
                function () {
                    calculateTotal();
                }
            );

            // TAMBAH BARANG
            $('#code').keypress(function (e) {

                if (e.which === 13) {

                    e.preventDefault();

                    let code = $(this).val();

                    $.ajax({

                        url: '{{ route("manager.sale.checkcode") }}',

                        method: 'POST',

                        data: {
                            _token: '{{ csrf_token() }}',
                            code: code
                        },

                        success: function (response) {

                            if (response.status === 'success') {

                                if (response.type === 'accessory') {

                                    table.row.add({

                                        code: response.data.code_acces,
                                        name: response.data.name,
                                        price: response.data.price,
                                        qty: 1,

                                        accessories_id: response.data.id,

                                        type: 'accessory',

                                        status: 'new'

                                    }).draw();

                                } else {

                                    table.row.add({

                                        code: response.data.no_seri,
                                        name: response.data.name,
                                        price: response.data.price,
                                        qty: 1,

                                        itemcategory_id: response.data.itemcategory_id,

                                        type: 'item',

                                        status: 'new'

                                    }).draw();

                                }

                                calculateTotal();

                                $('#code').val('');

                            } else {

                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }

                        }

                    });

                }

            });
// SIMPAN TRANSAKSI
            $('.btn-simpan').click(function () {

                let accessories = [];
                let items = [];

                table.rows().every(function () {

                    let data = this.data();

                    let qty = parseInt(
                        $(this.node()).find('.qty-input').val()
                    ) || 0;

                    // ACCESSORIES
                    if (data.type === 'accessory') {

                        accessories.push({

                            sale_detail_id: data.sale_detail_id ?? null,

                            accessories_id: data.accessories_id,

                            qty: qty,

                            status: data.status

                        });

                    }

                    // ITEMS
                    if (data.type === 'item') {

                        items.push({

                            sale_detail_id: data.sale_detail_id ?? null,

                            itemcategory_id: data.itemcategory_id,

                            no_seri: data.code,

                            name: data.name,

                            price: data.price,

                            qty: qty,

                            status: data.status

                        });

                    }

                });

                $.ajax({

                    url: '{{ route("manager.sale.update", $sale->id) }}',

                    method: 'PUT',

                    data: {

                        _token: '{{ csrf_token() }}',
                        customer_id: $('#customer_id').val(),
                        divisi_id: $('#single-select-optgroup-field').val(),
                        bank_id: $('#bank').val(),
                        no_po: $('#no_po').val(),
                        created_at: $('#created_at').val(),
                        deadlines: $('#deadlines').val(),
                        penerima: $('#penerima').val(),
                        description: $('#description').val(),
                        ppn: rupiahToNumber($('#ppn').val()),
                        pph: rupiahToNumber($('#pph').val()),
                        ongkir: rupiahToNumber($('#ongkir').val()),
                        diskon: rupiahToNumber($('#diskon').val()),
                        admin_fee: rupiahToNumber($('#admin_fee').val()),
                        fee: rupiahToNumber($('#fee').val()), // <- TAMBAHKAN INI
                        total_item: $('#total_item').val(),
                        total_price: rupiahToNumber($('#totalrp').val()),
                        bayar: rupiahToNumber($('#bayarrp').val()),
                        nominal_in: rupiahToNumber($('#nominal_in').val()),
                        accessories: accessories,
                        items: items
                    },
                    success: function (response) {

                        if (response.status === 'success') {

                            Swal.fire({

                                icon: 'success',

                                title: 'Berhasil',

                                text: response.message,

                                timer: 1500,

                                showConfirmButton: false

                            });

                            setTimeout(function () {

                                location.reload();

                            }, 1500);

                        } else {

                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );

                        }

                    },

                    error: function (xhr) {

                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ?? 'Terjadi kesalahan',
                            'error'
                        );

                    }

                });

            });
            // DELETE / RETURN
// DELETE / RETURN
            $(document).on('click', '.btn-delete', function () {

                let button = $(this);

                let row = button.closest('tr');

                let data = table.row(row).data();

                /*
                |--------------------------------------------------------------------------
                | AMBIL QTY
                |--------------------------------------------------------------------------
                */

                let qty = parseInt(
                    row.find('.qty-input').val()
                ) || 1;

                /*
                |--------------------------------------------------------------------------
                | JIKA BARANG BARU
                |--------------------------------------------------------------------------
                */

                if (data.status === 'new') {

                    table.row(row).remove().draw();

                    calculateTotal();

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | KONFIRMASI RETUR
                |--------------------------------------------------------------------------
                */

                Swal.fire({

                    title: 'Retur Barang?',
                    text: 'Stok akan dikembalikan',
                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'

                }).then((result) => {

                    if (result.isConfirmed) {

                        /*
                        |--------------------------------------------------------------------------
                        | URL RETURN
                        |--------------------------------------------------------------------------
                        */

                        let url = data.type === 'accessory'
                            ? `{{ route('sale.accessory.return', ':id') }}`
                            : `{{ route('sale.item.return', ':id') }}`;

                        url = url.replace(':id', data.sale_detail_id);

                        /*
                        |--------------------------------------------------------------------------
                        | AJAX RETURN
                        |--------------------------------------------------------------------------
                        */

                        $.ajax({

                            url: url,

                            method: 'POST',

                            data: {

                                _token: '{{ csrf_token() }}',

                                return_qty: qty

                            },

                            success: function (response) {

                                if (response.status === 'success') {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | HAPUS ROW TABLE
                                    |--------------------------------------------------------------------------
                                    */

                                    table.row(row).remove().draw();

                                    calculateTotal();

                                    Swal.fire({

                                        icon: 'success',

                                        title: 'Berhasil',

                                        text: response.message,

                                        timer: 1500,

                                        showConfirmButton: false

                                    });

                                } else {

                                    Swal.fire(
                                        'Error',
                                        response.message,
                                        'error'
                                    );

                                }

                            },

                            error: function (xhr) {

                                Swal.fire(
                                    'Error',
                                    xhr.responseJSON?.message ?? 'Terjadi kesalahan',
                                    'error'
                                );

                            }

                        });

                    }

                });

            });
        });

    </script>

@endpush
