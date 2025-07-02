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
            <form action="{{$url}}" method="POST" enctype="multipart/form-data" id="myForm">
                @csrf
                @isset($acces)
                    @method('PUT')
                @endisset
                <div class="mb-2">
                    <label class="col-form-label">Nama Aksesori</label>
                    <input type="text" name="name" class="form-control" value="{{isset($acces) ? $acces->name : null}}" placeholder="Masukkan Nama Aksesori">
                </div>
                <div class="mt-3 mb-2">
                    <label for="single-select-field" class="form-label">Divisi</label>
                    <select name="divisi_id" class="form-select" id="single-select-clear-field"
                            data-placeholder="Pilih Divisi">
                        @foreach($divisi as $div)
                            @if(isset($acces))
                                <option
                                    value="{{ $div->id }}" {{ $div->divisi_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                            @else
                                <option value=""></option>
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class=" mb-2">
                    <label class="col-form-label">Stok</label>
                        <input type="number" name="stok" class="form-control"
                               value="{{isset($acces) ? $acces->stok : null}}" placeholder="0">
                </div>
                <div class=" mb-2">
                    <label class="col-form-label">Capital Price</label>
                    <div class="input-group"><span class="input-group-text" id="basic-addon1">Rp.</span>
                        <input type="text" name="capital_price" class="form-control" onkeyup="formatRupiah(this)"
                               value="{{isset($acces) ? $acces->capital_price : null}}" placeholder="0">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="col-form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                        <input type="text" name="price" class="form-control" value="{{isset($acces) ? $acces->price : null}}" placeholder="Masukkan Harga" onkeyup="formatRupiah(this)">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-dnd float-end" id="submitBtn">Simpan<i class="bx bx-save"></i> </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')

@endpush
