@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">{{ isset($pembelian) ? 'Edit' : 'Tambah' }} Pembelian</h5>
            <form action="{{ $url }}" method="POST">
                @csrf
                @isset($pembelian)
                    @method('PUT')
                @endisset

                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    {{ html()->select('supplier_id', $supplier, isset($pembelian) ? $pembelian->supplier_id : null)->class('form-control')->id('single-select-field')->placeholder("--Select Supplier--") }}
                </div>
                <input type="hidden" name="divisi_id" value="{{ Auth::user()->divisi_id }}">

                <div class="mb-3">
                    <label for="invoice" class="form-label">Invoice</label>
                    <select name="invoice" id="kode_msk" class="form-control" required>
                        <option value="">--Pilih Invoice--</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice }}">{{ $invoice }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="0" {{ isset($pembelian) && $pembelian->status == '0' ? 'selected' : '' }}>Lunas
                        </option>
                        <option value="1" {{ isset($pembelian) && $pembelian->status == '1' ? 'selected' : '' }}>Belum
                            Lunas
                        </option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-sm">
                        <h6 class="mb-3">Daftar Barang</h6>
                    </div>
                </div>

                <table class="table table-bordered" id="itemTable">
                    <thead>
                    <tr>
                        <th colspan="6" class="text-center">Items Yang Dibeli</th>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <th>No Seri</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>PPN</th>
                        <th>Qty</th>
                    </tr>
                    </thead>
                    <tbody id="itemTableBody">
                    </tbody>
                </table>
                <table class="table table-bordered" id="itemTable">
                    <thead>
                    <tr>
                        <th colspan="6" class="text-center">Accessories Yang Dibeli</th>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Kode</th>
                        @if(in_array(Auth::user()->divisi->kode, ['001', '002']))
                            <th>Harga Beli</th>
                        @endif
                        <th>Harga Jual</th>
                        @if(in_array(Auth::user()->divisi->kode, ['001', '002']))
                            <th>PPN</th>
                        @endif
                        <th>Qty</th>
                    </tr>
                    </thead>
                    <tbody id="accesTableBody">
                    </tbody>
                </table>

                @if(isset($pembelian))
                    <a href="{{ route('admin.pembelian.index') }}" class="btn btn-dnd btn-sm">Kembali</a>
                @endif
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@push('head')

@endpush

@push('js')
    <script>
        let itemIndex = {{ isset($pembelian) ? count($pembelian->itemin ?? []) : 1 }};
        let accesIndex = {{ isset($pembelian) ? count($pembelian->accessoriesin ?? []) : 1 }};

        // Data dari controller
        const items = @json($item);
        const accessories = @json($acces);
        const userHasAccess = {{ in_array(Auth::user()->divisi->kode, ['001', '002']) ? 'true' : 'false' }};

        // Fungsi untuk menampilkan item berdasarkan kode_msk
        function displayItemsBykode_msk(kode_msk) {
            const filteredItems = items.filter(item => item.kode_msk === kode_msk);

            const allItems = [...filteredItems]; // Gabungkan keduanya

            let tableContent = '';

            allItems.forEach((item, index) => {
                const isAccessory = !!item.accessories; // Cek apakah data ini dari AccessoriesIn
                tableContent += `
                <tr>
                    <td>
                        <input type="text" name="items[${itemIndex}][name]" class="form-control"
                            value="${item.name}" required readonly>
                    </td>
                    <td>
                        <input type="text" name="items[${itemIndex}][no_seri]" class="form-control"
                            value="${item.no_seri}" required readonly>
                    </td>
                     ${userHasAccess ? `<td><input type="number" name="items[${index}][capital_price]" class="form-control" value="${item.capital_price}"></td>` : ''}
                    <td>
                        <input type="number" name="items[${itemIndex}][price]" class="form-control"
                            value="${item.price}" required>
                    </td>
                     ${userHasAccess ? `<td><input type="number" name="items[${index}][ppn]" class="form-control" value="${item.ppn || ''}"></td>` : ''}
                    <td>
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control"
                            value="${item.qty ?? '1'}" required readonly>
                    </td>
                </tr>
                `;
                itemIndex++;
            });

            document.getElementById('itemTableBody').innerHTML = tableContent;
        }

        function displayAccesBykode_msk(kode_msk) {
            const filteredAccessories = accessories.filter(acces => acces.kode_msk === kode_msk);

            const allAcces = [...filteredAccessories]; // Gabungkan keduanya

            let tableContent = '';

            allAcces.forEach((acces, index) => {
                tableContent += `
                <tr>
                    <td>
                        <input type="text" name="acces[${accesIndex}][name]" class="form-control"
                            value="${acces.accessories.name}" required readonly>
                    </td>
                    <td>
                        <input type="text" name="acces[${accesIndex}][code_acces]" class="form-control"
                            value="${acces.accessories.code_acces}" required readonly>
                    </td>
                    ${userHasAccess ? `<td><input type="number" name="acces[${index}][capital_price]" class="form-control" value="${acces.capital_price}"></td>` : ''}
                    <td>
                        <input type="number" name="acces[${accesIndex}][price]" class="form-control"
                            value="${acces.price}" required>
                    </td>
                    ${userHasAccess ? `<td><input type="number" name="acces[${index}][ppn]" class="form-control" value="${acces.ppn || ''}"></td>` : ''}
                    <td>
                        <input type="number" name="acces[${accesIndex}][qty]" class="form-control"
                            value="${acces.qty}" required readonly>
                    </td>
                </tr>
                `;
                accesIndex++;  // Pastikan accesIndex bertambah setiap kali aksesori ditambahkan
            });

            document.getElementById('accesTableBody').innerHTML = tableContent;
        }

        // Event listener saat invoice dipilih
        document.getElementById('kode_msk').addEventListener('change', function () {
            const selectedkode_msk = this.value;
            if (selectedkode_msk) {
                displayItemsBykode_msk(selectedkode_msk);
                displayAccesBykode_msk(selectedkode_msk);
            } else {
                document.getElementById('itemTableBody').innerHTML = ''; // Kosongkan jika tidak ada invoice
                document.getElementById('accesTableBody').innerHTML = ''; // Kosongkan jika tidak ada invoice
            }
        });

        // **Saat halaman dimuat dalam mode edit, isi tabel jika ada pembelian**
        document.addEventListener("DOMContentLoaded", function () {
            let selectedInvoice = "{{ isset($pembelian) ? $pembelian->invoice : '' }}";
            if (selectedInvoice) {
                document.getElementById('kode_msk').value = selectedInvoice; // Pilih invoice
                displayItemsBykode_msk(selectedInvoice);
                displayAccesBykode_msk(selectedInvoice);
            }
        });
    </script>
@endpush

