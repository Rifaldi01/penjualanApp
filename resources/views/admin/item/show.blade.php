@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Item {{$cat->name}}</h4>
                    </div>
                </div>
                <div class="col-6 mt-3">
                    <div class="container">
                        <a href="{{route('admin.item.index')}}" class="btn btn-warning float-end me-3 shadow">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>No Seri</th>
                        <th>Name Item</th>
                        <th></th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($item as $key => $data)
                        <tr>
                            <th>{{$key +1}}</th>
                            <td>{{$data->no_seri}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->divisi->name}}</td>
                            <td>{{formatRupiah($data->price)}}</td>
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
