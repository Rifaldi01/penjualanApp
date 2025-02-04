@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="container mt-3">
            <div class="card-head">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Supplier Management</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-end me-2">
                            <button class="btn btn-dnd btn-sm mb-3 bx bx-plus" id="createNewSupplier"></button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Name</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="supplierTableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="supplierForm" name="supplierForm">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalHeading"></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" name="kode" id="kode" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="text" name="telepon" id="telepon" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            loadSuppliers(); // Memuat data supplier saat halaman dibuka

            function loadSuppliers() {
                $.ajax({
                    url: "{{ route('admin.supplier.index') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        let suppliers = response;
                        let rows = "";
                        $.each(suppliers, function(index, supplier) {
                            rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${supplier.kode}</td>
                            <td>${supplier.name}</td>
                            <td>${supplier.alamat}</td>
                            <td>${supplier.telepon}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editSupplier" data-id="${supplier.id}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteSupplier" data-id="${supplier.id}">Delete</button>
                            </td>
                        </tr>`;
                        });
                        $("#supplierTableBody").html(rows);
                    }
                });
            }

            // Tambah Supplier
            $('#createNewSupplier').click(function () {
                $('#supplierForm').trigger("reset");
                $('#modalHeading').html("Add Supplier");
                $('#ajaxModal').modal('show');
                $('#id').val('');
            });

            // Edit Supplier
            $('body').on('click', '.editSupplier', function () {
                let id = $(this).data('id');
                $.get("{{ route('admin.supplier.index') }}/" + id + "/edit", function (data) {
                    $('#modalHeading').html("Edit Supplier");
                    $('#ajaxModal').modal('show');
                    $('#id').val(data.id);
                    $('#kode').val(data.kode);
                    $('#name').val(data.name);
                    $('#alamat').val(data.alamat);
                    $('#telepon').val(data.telepon);
                });
            });

            // Simpan Supplier
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
                let id = $('#id').val();
                let url = "{{ route('admin.supplier.store') }}";
                let type = "POST";

                if (id) {
                    url = "{{ route('admin.supplier.update', ':id') }}".replace(':id', id);
                    type = "PUT";
                }

                $.ajax({
                    data: $('#supplierForm').serialize(),
                    url: url,
                    type: type,
                    dataType: 'json',
                    success: function (data) {
                        $('#supplierForm').trigger("reset");
                        $('#ajaxModal').modal('hide');
                        loadSuppliers();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.success,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#saveBtn').html('Save');
                    },
                    error: function (data) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.responseJSON.message,
                        });
                        $('#saveBtn').html('Save');
                    }
                });
            });

            // Hapus Supplier
            $('body').on('click', '.deleteSupplier', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('admin.supplier.destroy', ':id') }}".replace(':id', id),
                            success: function (data) {
                                loadSuppliers();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.success,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function (data) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.responseJSON.message,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
