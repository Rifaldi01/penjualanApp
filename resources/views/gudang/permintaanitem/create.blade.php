@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="container">
            <div class="card-head mt-2">
                <h4>Form Permintaan Item</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('gudang.permintaanitem.store') }}" method="POST">
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
                            <th>No Seri</th>
                            <th>Jumlah</th>
                            <th width="2%">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select name="item_in_id[]" class="form-control single-select-field @error('item_in_id') is-invalid @enderror" style="width: 100%" onchange="updateNoSeri(this)">
                                    <option value="">-- Pilih Item --</option>
                                </select>
                                @error('item_in_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="no-seri"></td> <!-- Kolom untuk menampilkan no_seri -->
                            <td>
                                <input type="number" name="jumlah[]" class="form-control" value="1" required>
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
                <select name="item_in_id[]" class="form-control single-select-field" style="width: 100%" onchange="updateNoSeri(this)">
                    <option value="">-- Pilih Item --</option>
                    ${getAccessoriesOptions()}
                </select>
            </td>
            <td class="no-seri"></td> <!-- Kolom untuk menampilkan no_seri -->
            <td>
                <input type="number" name="jumlah[]" class="form-control" value="1" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-item bx bx-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Barang"></button>
            </td>
        </tr>`;
            document.querySelector('#itemTable tbody').insertAdjacentHTML('beforeend', newRow);

            // Inisialisasi Select2 pada dropdown baru
            initializeSelect2();
        });

        // Event delegation untuk tombol hapus
        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-item')) {
                event.target.closest('tr').remove();
            }
        });

        // Fungsi untuk mengupdate no_seri setelah memilih item
        function updateNoSeri(selectElement) {
            // Mendapatkan nilai item yang dipilih
            const itemId = selectElement.value;

            // Mencari data item berdasarkan itemId
            const item = accessoriesData.find(item => item.id == itemId);

            // Mendapatkan kolom no-seri pada baris yang sesuai
            const noSeriCell = selectElement.closest('tr').querySelector('.no-seri');

            if (item) {
                // Menampilkan no_seri
                noSeriCell.textContent = item.no_seri || '-';
            } else {
                // Jika item tidak ditemukan, menampilkan tanda '-'
                noSeriCell.textContent = '-';
            }
        }

        // Fungsi untuk mendapatkan opsi aksesori dalam bentuk HTML
        function getAccessoriesOptions() {
            return accessoriesData.map(item => {
                return `<option value="${item.id}">${item.name} - ${item.no_seri}</option>`;
            }).join('');
        }

        // Fungsi untuk inisialisasi Select2
        function initializeSelect2() {
            $('.single-select-field').select2({
                theme: 'bootstrap-5', // Gunakan tema sesuai
            });
        }

        // Fungsi untuk mengambil aksesori dari divisi asal
        document.getElementById('divisi_id_asal').addEventListener('change', function () {
            let divisiId = this.value;
            if (divisiId) {
                fetchAccessories(divisiId);
            }
        });

        // Fungsi untuk mengambil aksesori dari divisi asal
        function fetchAccessories(divisiId) {
            fetch(`/gudang/minta/item/${divisiId}`)
                .then(response => response.json())
                .then(data => {
                    accessoriesData = data;  // Menyimpan data aksesori
                    updateAccessoriesDropdown();
                });
        }

        // Fungsi untuk mengupdate dropdown aksesori dengan data yang dimuat
        function updateAccessoriesDropdown() {
            const accessoriesSelect = document.querySelectorAll('select[name="item_in_id[]"]');
            accessoriesSelect.forEach(select => {
                select.innerHTML = '<option value="">-- Pilih Item --</option>';
                select.innerHTML += getAccessoriesOptions();
            });

            // Inisialisasi ulang Select2 setelah data diupdate
            initializeSelect2();
        }

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Inisialisasi Select2 saat pertama kali halaman dimuat
        initializeSelect2();
    </script>
@endpush
