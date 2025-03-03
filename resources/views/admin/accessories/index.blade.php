@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Nama Divisi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="lni lni-apartment"></i></a></li>
                    <li id="breadcrumbDivisiName" class="breadcrumb-item active" aria-current="page">{{ $userDivisi }}</li>
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
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Accessories</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="accessoriesTable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Nama Accessories</th>
                        <th>Divisi</th>
                        <th>Price</th>
                        <th>Code Accessories</th>
                        <th>Stok</th>
                    </tr>
                    </thead>
                    <tbody></tbody> <!-- Kosong karena akan diisi via AJAX -->
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
            });

            let divisiId = ''; // Default kosong (menampilkan semua data)

            // Inisialisasi DataTable dengan AJAX
            let table = $('#accessoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: function(data, callback, settings) {
                    $.ajax({
                        url: `/admin/accessories/filter`,
                        data: {
                            divisi_id: divisiId, // Kirim divisi yang dipilih ke server
                            start: data.start, // DataTable parameter
                            length: data.length // DataTable parameter
                        },
                        success: function(response) {
                            callback({
                                draw: data.draw,
                                recordsTotal: response.recordsTotal,
                                recordsFiltered: response.recordsFiltered,
                                data: response.data
                            });
                        },
                        error: function(error) {
                            console.error('Error fetching accessories:', error);
                        }
                    });
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'divisi_name', name: 'divisi_name' },
                    { data: 'price', name: 'price' },
                    { data: 'code_acces', name: 'code_acces' },
                    { data: 'stok', name: 'stok' }
                ]
            });

            // Event listener untuk filter divisi
            $('#divisiFilter').on('change', function () {
                divisiId = this.value;
                const breadcrumbDivisiName = document.getElementById('breadcrumbDivisiName');
                const selectedOption = this.options[this.selectedIndex].text;

                breadcrumbDivisiName.textContent = divisiId ? selectedOption : '{{ $userDivisi }}';

                // Reload DataTable dengan filter divisi
                table.ajax.reload();
            });
        });
    </script>
@endpush
