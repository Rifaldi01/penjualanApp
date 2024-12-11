@extends('layouts.master')
@section('content')
    @if (isset($user))
        <h4>Edit  <i class="bx bx-edit-alt"></i></h4>
    @else
        <h4>Tambah  <i class="bx bx-plus-circle"></i></h4>
    @endif
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="container">
                <form action="{{$url}}" method="post">
                    @csrf
                    @isset($user)
                        @method('PUT')
                    @endif
                    <label for="LastName" class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" id="LastName" placeholder=" Nama" value="{{isset($user) ? $user->name : null }}">
                    <label for="LastName" class="form-label mt-3">Email</label>
                    <input type="email" name="email" class="form-control" id="LastName" placeholder="example@gmail.com" value="{{isset($user) ? $user->email : null }}" >
                    <label for="LastName" class="form-label mt-3">Phone</label>
                    <input type="number" name="phone" class="form-control" id="LastName" placeholder="08XXXXXXXXXX" value="{{isset($user) ? $user->phone : null }}" >
                    <div class="mt-3">
                        <label for="single-select-field" class="form-label">Roles</label>
                        <select class="form-select" id="single-select-field" required name="role" data-placeholder="Choose one thing">
                            <option value="">---Select---</option>
                            @foreach($role as $roles)
                                @if(isset($user) && $user->roles->contains('name', $roles->name))
                                    <option value="{{ $roles->name }}" selected>{{ $roles->name }}</option>
                                @else
                                    <option value="{{ $roles->name }}">{{ $roles->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>


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
