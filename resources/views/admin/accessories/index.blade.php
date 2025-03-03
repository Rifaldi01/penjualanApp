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
            }); // Mengaktifkan select2 pada divisi filter

            // Event listener untuk perubahan divisi
            $('#divisiFilter').on('change', function () {
                const divisiId = this.value;
                const breadcrumbDivisiName = document.getElementById('breadcrumbDivisiName');
                const selectedOption = this.options[this.selectedIndex].text;

                // Update nama divisi di breadcrumb
                if (divisiId) {
                    breadcrumbDivisiName.textContent = selectedOption;
                } else {
                    breadcrumbDivisiName.textContent = '{{ $userDivisi }}'; // Default ke divisi user login
                }

                // Fetch data accessories berdasarkan divisi
                fetch(`/admin/accessories/filter/${divisiId}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#accessoriesTable tbody');
                        tbody.innerHTML = '';

                        // Cek jika data ada untuk divisi yang dipilih
                        if (data.length > 0) {
                            data.forEach((item, index) => {
                                const priceFormatted = new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0, // Menghapus angka di belakang koma
                                    maximumFractionDigits: 0
                                }).format(item.price);

                                const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.name}</td>
                                <td>${item.divisi.name}</td>
                                <td>${priceFormatted}</td>
                                <td>${item.code_acces}</td>
                                <td>${item.stok}</td>
                            </tr>
                        `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="6" class="text-center">Tidak ada data untuk divisi yang dipilih</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching accessories:', error);
                        const tbody = document.querySelector('#accessoriesTable tbody');
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Terjadi kesalahan dalam mengambil data</td></tr>`;
                    });
            });

            // Initialize DataTable
            $('#accessoriesTable').DataTable({
                paginate: false,
            });
        });

    </script>
@endpush
