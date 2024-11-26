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
                    <label for="code_access" class="col-lg-2 mt-2">
                        <strong> Kode Accessories : </strong>
                    </label>
                    <div class="col-lg-5">
                        <div class="input-group">
                            <input type="hidden" name="id" id="idAccess">
                            <input type="text" class="form-control" name="code_access" id="code_access" placeholder="Enter Code Accessories">
                        </div>
                    </div>
                </div>
            </form>
            <table class="table mt-2 table-stok">
                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Code Acces</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody id="accessoriesTableBody"></tbody>
            </table>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm float-end btn-simpan"><i class="bx bx-save"></i> Save</button>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            let table = $('.table-stok').DataTable({
                processing: true,
                autoWidth: false,
                url: '{{ route('gudang.acces.editmultiple') }}',
                dataSrc: 'data',
                dataType : 'json',
                columns: [
                    { data: null, searchable: false, sortable: false, render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'code_acces' },
                    { data: 'name' },
                    { data: 'price' },
                    { data: 'stok', render: function(data, type, row) {
                            return '<input type="number" name="stok[0][]" class="form-control stok-input" value="' + data + '" />';
                        }},
                    { data: null, searchable: false, sortable: false, render: function(data, type, row) {
                            return '<button class="btn btn-danger btn-sm bx bx-trash" onclick="removeRow(this)"></button>';
                        }
                    }
                ],
                dom: 'Brt',
                bSort: false,
                paginate: false,
                lengthChange: false,
                buttons: []
            });

            $('#code_access').on('keypress', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let codeAccess = $(this).val();
                    if (codeAccess) {
                        $.ajax({
                            url: '{{ route('gudang.acces.checkcode') }}',
                            method: 'POST',
                            data: { _token: '{{ csrf_token() }}', code_access: codeAccess },
                            success: function (response) {
                                if (response.exists) {
                                    let found = false;
                                    table.rows().every(function () {
                                        let data = this.data();
                                        if (data.code_acces === codeAccess) {
                                            data.stok = parseInt(data.stok) + 1;
                                            this.data(data).draw();
                                            found = true;
                                            return false;
                                        }
                                    });
                                    if (!found) {
                                        table.row.add({
                                            DT_RowIndex: table.data().count() + 1,
                                            code_acces: response.data.code_acces,
                                            name: response.data.name,
                                            price: response.data.price,
                                            stok: 1,
                                            action: '<button class="btn btn-danger btn-sm bx bx-trash" onclick="removeRow(this)"></button>'
                                        }).draw(true);
                                    }
                                    $('#code_access').val('');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Code tidak terdaftar!'
                                    });
                                    $('#code_access').val('');
                                }
                            }
                        });
                    }
                }
            });

            $('.form-produk').on('submit', function (e) {
                e.preventDefault();
                let accessoriesData = [];
                table.rows().every(function () {
                    let data = this.data();
                    data.stok = $(this.node()).find('input.stok-input').val();
                    accessoriesData.push(data);
                });
                $.ajax({
                    url: '{{ route('gudang.acces.updatemultiple') }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', accessories: accessoriesData },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Success!',
                                'Save Accessories successfully!',
                                'success'
                            );
                            table.clear().draw();
                        }
                    }
                });
            });

            $('.btn-simpan').on('click', function () {
                $('.form-produk').submit();
            });
        });

        function removeRow(button) {
            let table = $('.table-stok').DataTable();
            table.row($(button).parents('tr')).remove().draw();
        }
    </script>
@endpush
