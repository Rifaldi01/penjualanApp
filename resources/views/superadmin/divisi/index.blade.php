@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">Daftar Divisi</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a href="{{route('superadmin.divisi.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"
                    ><i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="2%">Logo</th>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Invoice Format</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="9%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($divisi as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>
                                <img src="{{asset('images/logo/'. $data->logo)}}" alt="{{$data->name}}" width="100%">
                            </td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->kode}}</td>
                            <td>{{$data->inv_format}}</td>
                            <th>Active/tidak</th>
                            <td>
                                <a href="{{route('superadmin.divisi.edit', $data->id)}}" class="btn btn-sm btn-warning bx bx-edit"></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')

@endpush
