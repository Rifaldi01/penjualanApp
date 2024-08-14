@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Accessories</h4>
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
                        <th>Name</th>
                        <th>Price</th>
                        <th>Code Accessories</th>
                        <th>Stok</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($acces as $key => $data)
                        <tr>
                            <th>{{$key +1}}</th>
                            <td>{{$data->name}}</td>
                            <td>{{formatRupiah($data->price)}}</td>
                            <td>{{$data->code_acces}}</td>
                            <td>{{$data->stok}}</td>
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
