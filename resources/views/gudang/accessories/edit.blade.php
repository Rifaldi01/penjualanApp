@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <h3>Add Accessories<span class="bx bx-barcode"></span></h3>
                <hr>
            </div>
        </div>
        <div class="card-body">
            <form class="form-produk">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-2 mt-2">
                        <strong>Kode/Nama Accessories :</strong>
                    </label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control" id="code_access" placeholder="Enter Code Accessories">
                    </div>
                </div>
            </form>

            <table class="table mt-2 table-stok">
                <thead>
                <tr>
                    <th>Code Acces</th>
                    <th>Name</th>
                    <th>Asal Pembelian</th>
                    <th>Price</th>
                    <th>Stok</th>
                    <th>Kode/Invoice</th>
                    <th>Tanggal Masuk</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="box-footer">
                <button type="button" class="btn btn-primary btn-sm float-end btn-simpan">
                    <i class="bx bx-save"></i> Save
                </button>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function () {

            let table = $('.table-stok').DataTable({
                autoWidth: false,
                data: [],
                paging: false,
                searching: false,
                ordering: false,

                columns: [
                    { data: 'code_acces' },
                    { data: 'name' },
                    { data: 'region' },
                    {
                        data: 'price',
                        render: function (data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'stok',
                        render: function (data) {
                            return '<input type="number" class="form-control stok-input" value="'+data+'">';
                        }
                    },
                    {
                        data: 'kode_msk',
                        render: function () {
                            return '<input type="text" class="form-control kode_msk-input" placeholder="kode/invoice">';
                        }
                    },
                    {
                        data: 'date_in',
                        render: function () {
                            return '<input type="text" class="form-control datepicker date_in-input" placeholder="Tanggal Masuk">';
                        }
                    },
                    {
                        data: null,
                        render: function () {
                            return '<button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button>';
                        }
                    }
                ],

                // 🔥 INI YANG PALING PENTING
                createdRow: function (row) {
                    $(row).find('.datepicker').flatpickr({
                        dateFormat: "Y-m-d"
                    });
                }
            });

            function formatRupiah(amount) {
                let number_string = amount.toString();
                return 'Rp ' + number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // SCAN BARCODE / INPUT ENTER
            $('#code_access').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let codeAccess = $(this).val();

                    if (!codeAccess) return;

                    $.ajax({
                        url: '{{ route('gudang.acces.checkcode') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            code_access: codeAccess
                        },
                        success: function (response) {

                            if (response.exists) {
                                let found = false;

                                table.rows().every(function () {
                                    let data = this.data();

                                    if (data.code_acces === codeAccess) {
                                        data.stok = parseInt(data.stok) + 1;
                                        this.data(data).draw();
                                        found = true;
                                    }
                                });

                                if (!found) {
                                    table.row.add({
                                        code_acces: response.data.code_acces,
                                        name: response.data.name,
                                        region: response.data.region,
                                        price: response.data.price,
                                        stok: 1
                                    }).draw();
                                }

                                $('#code_access').val('');

                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });

            // SAVE DATA
            $('.btn-simpan').click(function () {

                let accessoriesData = [];

                table.rows().every(function () {
                    let row = $(this.node());

                    accessoriesData.push({
                        code_acces: this.data().code_acces,
                        stok: row.find('.stok-input').val(),
                        kode_msk: row.find('.kode_msk-input').val(),
                        date_in: row.find('.date_in-input').val()
                    });
                });

                $.ajax({
                    url: '{{ route('gudang.acces.updatemultiple') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        accessories: accessoriesData
                    },
                    success: function () {
                        Swal.fire('Success', 'Data berhasil disimpan', 'success');
                        table.clear().draw();
                    }
                });

            });

        });

        // HAPUS ROW
        function removeRow(btn) {
            let table = $('.table-stok').DataTable();
            table.row($(btn).parents('tr')).remove().draw();
        }
    </script>
@endpush
