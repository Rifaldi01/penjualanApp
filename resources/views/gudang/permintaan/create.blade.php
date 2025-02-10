@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="container">
            <div class="card-head mt-2">
                <h4>Form Permintaan Accessories</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('gudang.permintaan.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="divisi_id_asal" class="mb-2">Divisi Asal</label>
                        <select name="divisi_id_asal" id="divisi_id_asal" class="form-control @error('divisi_id_asal') is-invalid @enderror">
                            <option value="">-- Pilih Divisi Asal --</option>
                            @foreach($divisi as $divisi_item)
                                <option value="{{ $divisi_item->id }}" {{ old('divisi_id_asal') == $divisi_item->id ? 'selected' : '' }}>
                                    {{ $divisi_item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('divisi_id_asal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <h6 class="mb-2">Daftar Barang</h6>
                        </div>
                        <div class="col-sm">
                            <div class="float-end">
                                <button type="button" class="btn btn-dnd btn-sm bx bx-plus mb-2" id="addItem" data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Barang"></button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered" id="itemTable">
                        <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select name="accessories_id[]" id="accessories_id" class="form-control single-select-field @error('accessories_id') is-invalid @enderror">
                                    <option value="">-- Pilih Aksesoris --</option>
                                </select>
                                @error('accessories_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" name="jumlah[]" class="form-control" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-item bx bx-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Barang"></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary mt-2">Kirim</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Variabel untuk menyimpan data aksesori
        let accessoriesData = [];

        // Fungsi untuk menambahkan baris baru
        document.getElementById('addItem').addEventListener('click', function () {
            let newRow = `
    <tr>
        <td>
            <select name="accessories_id[]" class="form-control single-select-field @error('accessories_id') is-invalid @enderror">
                <option value="">-- Pilih Aksesoris --</option>
                ${getAccessoriesOptions()}
            </select>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control" required>
        </td>
        <td>
            <button type="button" class="btn btn-danger remove-item bx bx-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Barang"></button>
        </td>
    </tr>`;
            document.querySelector('#itemTable tbody').insertAdjacentHTML('beforeend', newRow);

            // Inisialisasi Select2 pada dropdown baru
            initializeSelect2();
        });

        // Fungsi untuk menghapus baris
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('tr').remove();
            }
        });

        // Event listener untuk memuat aksesori berdasarkan divisi asal
        document.getElementById('divisi_id_asal').addEventListener('change', function () {
            let divisiId = this.value;
            if (divisiId) {
                fetchAccessories(divisiId);
            }
        });

        // Fungsi untuk mengambil aksesori dari divisi asal
        function fetchAccessories(divisiId) {
            fetch(`/gudang/minta/accessories/${divisiId}`)
                .then(response => response.json())
                .then(data => {
                    accessoriesData = data;  // Menyimpan data aksesori
                    updateAccessoriesDropdown();
                });
        }

        // Fungsi untuk mengupdate dropdown aksesori dengan data yang dimuat
        function updateAccessoriesDropdown() {
            const accessoriesSelect = document.querySelectorAll('select[name="accessories_id[]"]');
            accessoriesSelect.forEach(select => {
                select.innerHTML = '<option value="">-- Pilih Aksesoris --</option>';
                select.innerHTML += getAccessoriesOptions();
            });

            // Inisialisasi ulang Select2 setelah data diupdate
            initializeSelect2();
        }

        // Fungsi untuk mendapatkan opsi aksesori dalam bentuk HTML
        function getAccessoriesOptions() {
            return accessoriesData.map(item => {
                return `<option value="${item.id}">${item.name} - ${item.price} (Stok: ${item.stok})</option>`;
            }).join('');
        }

        // Fungsi untuk inisialisasi Select2
        function initializeSelect2() {
            $('.single-select-field').select2({
                theme: 'bootstrap-5', // Gunakan tema sesuai
            });
        }

        // Inisialisasi Select2 saat pertama kali halaman dimuat
        initializeSelect2();
    </script>
@endpush
