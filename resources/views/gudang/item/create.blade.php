@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                @if(isset($item))
                    <h3 class="mb-4 ms-3">Edit Item<i class="bx bx-edit"></i></h3>
                @else
                    <h3 class="mb-4 ms-3">Tambah Item<i class="bx bx-user-plus"></i></h3>
                @endif
                <hr>
            </div>

        </div>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-danger"><i class='bx bxs-message-square-x'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-danger">Error</h6>
                            <div>
                                <div>{{ $error }}</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        @endif
        <form action="{{$url}}" method="POST">

            <div class="card-body">
                @csrf
                @isset($item)
                    @method('PUT')
                @endisset
                <button type="button" class="btn btn-success mb-2 float-end me-2" id="addRow">+ Tambah</button>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Kategori</th>
                        <th>No Seri</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <tr>
                        <td>
                            <input type="hidden" name="id[]" value="{{ $item->id ?? '' }}">
                            <input type="text" name="name[]" class="form-control"
                                   value="{{ $item->name ?? '' }}">
                        </td>

                        <td>
                            <input type="text" name="created_at[]" class="form-control datepicker"
                                   value="{{ $item->created_at ?? '' }}">
                        </td>

                        <td>
                            <input type="text" name="kode_msk[]" class="form-control"
                                   value="{{ isset($item) ? optional($item->itemIn)->kode_msk : '' }}">
                        </td>

                        <td>
                            <select name="itemcategory_id[]" class="form-select">
                                @foreach($cat as $category)
                                    <option value="{{ $category->id }}"
                                        {{ isset($item) && $item->itemcategory_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text" name="no_seri[]" class="form-control"
                                   value="{{ $item->no_seri ?? '' }}">
                        </td>

                        <td>
                            <button type="button" class="btn btn-danger" onclick="removeRow(this)">Hapus</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary float-end mb-2 me-2"><i class="bx bx-save"></i>Simpan
                </button>
                <a href="{{route('gudang.item.index')}}" class="btn btn-warning float-end me-2"><i
                        class="bx bx-list-ul"></i>Kembali</a>
            </div>
        </form>
    </div>
@endsection

@push('head')

@endpush
@push('js')
    <script>
        $(document).ready(function () {
            $('#submitBtnItem').click(function () {
                // Disable button dan ubah teksnya
                $(this).prop('disabled', true).text('Loading...');

                // Kirim form secara manual
                $('#myFormItem').submit();
            });
        });
    </script>
    <script>
        $('#addRow').click(function () {
            let row = `
                <tr>
                    <td>
                        <input type="hidden" name="id[]" value="">
                        <input type="text" name="name[]" class="form-control">
                    </td>
                    <td><input type="text" name="created_at[]" class="form-control datepicker"></td>
                    <td><input type="text" name="kode_msk[]" class="form-control"></td>
                    <td>
                        <select name="itemcategory_id[]" class="form-select">
                            @foreach($cat as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
            </select>
        </td>
        <td><input type="text" name="no_seri[]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Hapus</button></td>
    </tr>
`;
            $('#table-body').append(row);

            // re-init datepicker
            $(".datepicker").flatpickr({dateFormat: "Y-m-d"});
        });

        function removeRow(btn) {
            $(btn).closest('tr').remove();
        }

        $(document).ready(function () {
            $('#submitBtnItem').click(function (event) {

                // Hapus titik dari input harga
                let priceInput = $('input[name="price"]');
                let priceValue = priceInput.val().replace(/\./g, '');
                priceInput.val(priceValue);

                // Kirim form secara manual
                $('#myFormItem').submit();
            });
        });

        function formatRupiahItem(element) {
            let value = element.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            element.value = rupiah;
        }
    </script>
@endpush
