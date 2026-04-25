@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <h3>Add Accessories <span class="bx bx-barcode"></span></h3>
                <hr>
            </div>
        </div>

        <div class="card-body">

            {{-- INPUT ATAS --}}
            <div class="form-group row mb-3">
                <label class="col-lg-2 mt-2">
                    <strong>Kode/Nama Accessories :</strong>
                </label>
                <div class="col-lg-5">
                    <input type="text"
                           class="form-control scan-accessories"
                           placeholder="Enter Code Accessories">
                </div>
            </div>

            {{-- TABLE --}}
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

            {{-- INPUT BAWAH --}}
            <div class="form-group row mt-3">
                <label class="col-lg-2 mt-2">
                    <strong>Kode/Nama Accessories :</strong>
                </label>
                <div class="col-lg-5">
                    <input type="text"
                           class="form-control scan-accessories"
                           placeholder="Enter Code Accessories">
                </div>
            </div>

            <div class="box-footer mt-3">
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
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                data: [],

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
                            return '<input type="number" class="form-control stok-input" value="' + data + '">';
                        }
                    },

                    {
                        data: null,
                        render: function () {
                            return '<input type="text" class="form-control kode_msk-input" placeholder="kode/invoice">';
                        }
                    },

                    {
                        data: null,
                        render: function () {
                            return '<input type="text" class="form-control datepicker date_in-input" placeholder="Tanggal Masuk">';
                        }
                    },

                    {
                        data: null,
                        render: function () {
                            return '<button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>';
                        }
                    }
                ],

                createdRow: function (row) {
                    $(row).find('.datepicker').flatpickr({
                        dateFormat: "Y-m-d"
                    });
                }
            });


            function formatRupiah(angka) {
                angka = parseInt(angka);
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }


            function cariAccessories(keyword, inputObj) {

                if (keyword == '') return;

                $.ajax({
                    url: '{{ route("gudang.acces.checkcode") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        code_access: keyword
                    },

                    success: function (response) {

                        if (response.exists) {

                            let found = false;

                            table.rows().every(function () {

                                let data = this.data();

                                if (data.code_acces == response.data.code_acces) {

                                    let row = $(this.node());
                                    let qty = row.find('.stok-input').val();

                                    row.find('.stok-input').val(parseInt(qty) + 1);

                                    found = true;
                                }
                            });

                            // GANTI seluruh blok if (!found) menjadi ini

                            if (!found) {

                                // simpan semua data lama beserta isi input manual user
                                let semuaData = [];

                                table.rows().every(function () {

                                    let row = $(this.node());

                                    semuaData.push({
                                        code_acces: this.data().code_acces,
                                        name: this.data().name,
                                        region: this.data().region,
                                        price: this.data().price,

                                        stok: row.find('.stok-input').val(),
                                        kode_msk: row.find('.kode_msk-input').val(),
                                        date_in: row.find('.date_in-input').val()
                                    });
                                });

                                // tambahkan data baru ke paling atas
                                semuaData.unshift({
                                    code_acces: response.data.code_acces,
                                    name: response.data.name,
                                    region: response.data.region,
                                    price: response.data.price,
                                    stok: 1,
                                    kode_msk: '',
                                    date_in: ''
                                });

                                // reload table
                                table.clear().rows.add(semuaData).draw();
                            }

                            $('.scan-accessories').val('');
                            $('.scan-accessories').first().focus();

                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            }


            // INPUT ATAS / BAWAH BISA DIGUNAKAN
            $(document).on('keypress', '.scan-accessories', function (e) {

                if (e.which == 13) {
                    e.preventDefault();

                    let keyword = $(this).val();
                    cariAccessories(keyword, $(this));
                }

            });


            // HAPUS ROW
            $(document).on('click', '.btn-hapus', function () {
                table.row($(this).parents('tr')).remove().draw();
            });


            // SAVE
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
                    url: '{{ route("gudang.acces.updatemultiple") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        accessories: accessoriesData
                    },

                    success: function () {
                        Swal.fire('Success', 'Data berhasil disimpan', 'success');
                        table.clear().draw();
                        $('.scan-accessories').val('');
                    },

                    error: function () {
                        Swal.fire('Error', 'Gagal menyimpan data', 'error');
                    }
                });

            });

        });
    </script>
@endpush
