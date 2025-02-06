@extends('layouts.master')
@section('content')
        @if (isset($divisi))
            <h4>Edit  <i class="bx bx-edit-alt"></i></h4>
        @else
            <h4>Tambah  <i class="bx bx-plus-circle"></i></h4>
        @endif
        <hr>
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <form action="{{$url}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @isset($divisi)
                            @method('PUT')
                        @endif
                        <label for="LastName" class="form-label">Nama Divisi</label>
                        <input type="text" name="name" class="form-control" placeholder=" Nama Divisi" value="{{isset($divisi) ? $divisi->name : null }}">
                        <label for="LastName" class="form-label mt-3">Kode Divisi</label>
                        <input type="text" name="kode" class="form-control" placeholder="XXXX" value="{{isset($divisi) ? $divisi->kode : null }}" >
                        <label for="LastName" class="form-label mt-3">Format Invoice</label>
                        <input type="text" name="inv_format" class="form-control" placeholder="INV/DND/XXX/XX" value="{{isset($divisi) ? $divisi->inv_format : null }}" >
                        <label for="LastName" class="form-label mt-3">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="{{isset($divisi) ? $divisi->email : null }}" >
                        <label for="LastName" class="form-label mt-3">phone</label>
                        <input type="number" name="phone" class="form-control" placeholder="0XXXXXXXXXXX" value="{{isset($divisi) ? $divisi->phone : null }}" >
                        <label for="LastName" class="form-label mt-3">No. Rek Divisi</label>
                        <input type="text" name="no_rek" class="form-control" placeholder="XXXXXXX" value="{{isset($divisi) ? $divisi->no_rek : null }}" >
                        <label for="LastName" class="form-label mt-3">Alamat</label>
                        <textarea name="alamat" class="form-control">{{isset($divisi) ? $divisi->alamat : null }}</textarea>
                        <label for="LastName" class="form-label mt-3">Logo Divisi</label>
                        <input type="file" name="logo" class="form-control" accept="image/*" >

                        <div class="card-footer mt-3">
                            <button type="submit" class="btn btn-primary mt-3 float-end">Kirim</button>
                            <a href="{{route('superadmin.account.index')}}" class="btn btn-danger mt-3 me-2 float-end">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection

@push('head')

@endpush
@push('js')

@endpush
