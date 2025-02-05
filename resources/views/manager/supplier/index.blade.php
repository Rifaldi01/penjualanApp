@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Nama Divisi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="lni lni-apartment"></i></a></li>
                    <li id="breadcrumbDivisiName" class="breadcrumb-item active" aria-current="page">{{$divisiUser}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <select id="divisiFilter" class="form-select select2">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisi as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="container mt-3">
            <div class="card-head">
                <div class="row">
                    <div class="col-sm-6"> <h4>Supplier Management</h4></div>
                    <div class="col-sm-6">
                        <div class="float-end me-2">
                            <button class="btn btn-dnd btn-sm mb-3 bx bx-plus" id="createNewSupplier"></button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped" id="supplierTable">
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
                <tbody></tbody>
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
                            <label for="divisi_id" class="form-label">Divisi</label>
                            <select name="divisi_id" id="divisi" class="form-control single-select-field">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>

                        </div>
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
            var table = $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('manager.supplier.index') }}",
                    data: function(d) {
                        d.divisi_id = $('#divisiFilter').val(); // Kirim nilai divisi yang dipilih
                    }
                },
                columns: [
                    { data: 'id', name: 'id', render: function(data, type, row, meta) { return meta.row + 1; } }, // Ubah index agar berurutan
                    { data: 'kode', name: 'kode' },
                    { data: 'name', name: 'name' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'telepon', name: 'telepon' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });

            $('#divisiFilter').change(function() {
                var selectedDivisi = $('#divisiFilter option:selected').text(); // Ambil nama divisi yang dipilih
                $('#breadcrumbDivisiName').text(selectedDivisi); // Perbarui breadcrumb dengan nama divisi
                table.draw(); // Refresh tabel saat filter berubah
            });

            // Pilih divisi yang sudah dipilih sebelumnya saat halaman dimuat
            var selectedDivisi = $('#divisiFilter option:selected').text();
            $('#breadcrumbDivisiName').text(selectedDivisi); // Set breadcrumb sesuai divisi yang dipilih

            $('.single-select-field').select2({
                theme: 'bootstrap-5', // Gunakan tema bootstrap (jika tersedia)
                placeholder: 'Pilih Divisi',
                allowClear: true
            });

            $('#createNewSupplier').click(function () {
                $('#supplierForm').trigger("reset");
                $('#modalHeading').html("Add Supplier");
                $('#ajaxModal').modal('show');
                $('#id').val('');
            });

            $('body').on('click', '.editSupplier', function () {
                const id = $(this).data('id');
                $.get("{{ route('manager.supplier.index') }}/" + id + "/edit", function (data) {
                    $('#modalHeading').html("Edit Supplier");
                    $('#ajaxModal').modal('show');
                    $('#id').val(data.id);
                    $('#kode').val(data.kode);
                    $('#divisi').val(data.divisi_id);
                    $('#name').val(data.name);
                    $('#alamat').val(data.alamat);
                    $('#telepon').val(data.telepon);
                });
            });

            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
                const id = $('#id').val();
                let url = "{{ route('manager.supplier.store') }}";
                let type = "POST";

                if (id) {
                    url = "{{ route('manager.supplier.update', ':id') }}".replace(':id', id);
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
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.success,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#saveBtn').html('Save');
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan',
                        });
                        $('#saveBtn').html('Save');
                    }
                });
            });

            $('body').on('click', '.deleteSupplier', function () {
                const id = $(this).data('id');
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
                            url: "{{ route('manager.supplier.destroy', ':id') }}".replace(':id', id),
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.success,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON.message || 'Terjadi kesalahan',
                                });
                            }
                        });
                    }
                });
            });
        });

    </script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endpush
