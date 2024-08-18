@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Accessories Sale</h4>
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
                        <th>Kode produk</th>
                        <th>Name</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Customer</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($report as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{$data->accessories->code_acces}}</td>
                            <td>{{$data->accessories->name}}</td>
                            <td>{{formatRupiah($data->accessories->price)}}</td>
                            <td>{{$data->qty}}</td>
                            <td>{{formatRupiah($data->subtotal)}}</td>
                            <td>{{$data->sale->customer->name}}</td>
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
