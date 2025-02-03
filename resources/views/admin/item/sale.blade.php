@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Item Sale</h4>
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
                        <th>Invoice</th>
                        <th>No Seri</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Pembeli</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($sale as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{$data->sale->invoice}}</td>
                            <td>{{$data->itemCategory->name}}-{{$data->no_seri}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->itemCategory->name}}</td>
                            <td>{{ formatRupiah($data->price) }}</td>
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
