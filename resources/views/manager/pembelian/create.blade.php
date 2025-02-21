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
                    <label for="divisi_id" class="form-label">Divisi</label>
                    {{ html()->select('divisi_id', $divisi, isset($pembelian) ? $pembelian->divisi_id : null )->class('form-control')->id('single-select-optgroup-field')->placeholder("--Select Divisi--") }}
                </div>
                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    {{ html()->select('supplier_id', $supplier, isset($pembelian) ? $pembelian->supplier_id : null )->class('form-control')->id('single-select-field')->placeholder("--Select Supplier--") }}
                </div>
                <div class="mb-3">
                    <label for="invoice" class="form-label">Invoice</label>
                    <input type="text" name="invoice" class="form-control"
                           value="{{ $pembelian->invoice ?? '' }}" required>
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
                    <div class="col-sm">
                        <div class="float-end">
                            <button type="button" class="btn btn-dnd btn-sm bx bx-plus mb-3" id="addItem"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Barang"></button>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered" id="itemTable">
                    <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>PPN</th>
                        <th>Qty</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($pembelian))
                        @foreach($pembelian->itemBeli as $item)
                            <tr>
                                <td>
                                    <input type="text" name="items[{{ $loop->index }}][name]"
                                           class="form-control" value="{{ $item->name }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $loop->index }}][harga]"
                                           class="form-control" value="{{ $item->harga }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $loop->index }}][ppn]"
                                           class="form-control" value="{{ $item->ppn }}" >
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $loop->index }}][qty]"
                                           class="form-control" value="{{ $item->qty }}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger remove-item bx bx-trash"   data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Barang"></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input type="text" name="items[0][name]" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][harga]" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][ppn]" class="form-control" >
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]" class="form-control" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-item bx bx-trash"   data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Barang"></button>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                @if(isset($pembelian))
                    <a href="{{route('manager.pembelian.index')}}"
                       class="btn btn-dnd btn-sm  ">Kembali</a>
                @endif
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let itemIndex = {{ isset($pembelian) ? count($pembelian->itemBeli) : 1 }};
        document.getElementById('addItem').addEventListener('click', function () {
            let newRow = `
            <tr>
                <td><input type="text" name="items[${itemIndex}][name]" class="form-control" required></td>
                <td><input type="number" name="items[${itemIndex}][harga]" class="form-control" required></td>
                <td><input type="number" name="items[${itemIndex}][ppn]" class="form-control"></td>
                <td><input type="number" name="items[${itemIndex}][qty]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger remove-item bx bx-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="hapus Barang"></button></td>
            </tr>`;
            document.querySelector('#itemTable tbody').insertAdjacentHTML('beforeend', newRow);
            itemIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('tr').remove();
            }
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
