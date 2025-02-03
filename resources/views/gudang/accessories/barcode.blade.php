@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                @if(isset($acces))
                    <h3>Ubah Aksesori<span class="bx bx-barcode"></span></h3>
                @else
                    <h3>Tambah Barcode<span class="bx bx-barcode"></span></h3>
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
                            <h6 class="mb-0 text-danger">Kesalahan</h6>
                            <div>
                                <div>{{ $error }}</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endforeach
        @endif
        <div class="card-body p-4">
            <form action="{{$url}}" method="POST" enctype="multipart/form-data" id="myFormAcces">
                @csrf
                @isset($acces)
                    @method('PUT')
                @endisset
                <div class="mb-2">
                    <label class="col-form-label">Nama Aksesori</label>
                    <input type="text" name="name" class="form-control" value="{{isset($acces) ? $acces->name : null}}" placeholder="Masukkan Nama Aksesori">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-dnd float-end" id="submitBtnAcces">Simpan<i class="bx bx-save"></i> </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')
    <script>
        $(document).ready(function() {
            $('#submitBtnAcces').click(function(event) {
                // Nonaktifkan tombol dan ubah teksnya
                $(this).prop('disabled', true).text('Memuat...');

                $('#myFormAcces').submit();
            });
        });

        function formatRupiahAcces(element) {
            let value  = element.value.replace(/[^,\d]/g, '');
            let split  = value.split(',');
            let sisa   = split[0].length % 3;
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
