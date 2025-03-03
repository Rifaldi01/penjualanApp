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
                    <tbody>
                    @foreach ($acces as $key => $data)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->divisi->name }}</td>
                            <td>{{ formatRupiah($data->price) }}</td>
                            <td>{{ $data->code_acces }}</td>
                            <td>{{ $data->stok }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Inisialisasi Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
            });

            let divisiId = ''; // Variabel untuk menyimpan divisi yang dipilih

            // Inisialisasi DataTable dengan AJAX
            let table = $('#accessoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/admin/accessories/filter',
                    data: function (d) {
                        d.divisi_id = divisiId; // Mengirim divisi_id ke server
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'divisi.name', name: 'divisi.name' },
                    { data: 'price', name: 'price', render: function(data) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(data);
                        }
                    },
                    { data: 'code_acces', name: 'code_acces' },
                    { data: 'stok', name: 'stok' }
                ],
                order: [[0, 'asc']]
            });

            // Event listener untuk filter divisi
            $('#divisiFilter').on('change', function () {
                divisiId = this.value;
                const breadcrumbDivisiName = document.getElementById('breadcrumbDivisiName');
                const selectedOption = this.options[this.selectedIndex].text;

                // Update breadcrumb
                breadcrumbDivisiName.textContent = divisiId ? selectedOption : '{{ $userDivisi }}';

                // Perbarui tabel tanpa reset halaman
                table.ajax.reload(null, false);
            });
        });


    </script>
@endpush
