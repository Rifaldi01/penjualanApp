@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Accessories In</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example4" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th>Code Access</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th class="text-center" width="10%">Stok</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(request()->routeIs('manager.acces.accesin'))
                    @foreach($accesin as $key => $data)
                        <tr>
                            <td class="text-center">{{tanggal($data->date_in)}}</td>
                            <td>{{$data->accessories->code_acces}}</td>
                            <td>
                                <a class="text-dark">{{$data->accessories->name}}</a>
                            </td>
                            <td>
                                <a class="text-dark">{{formatRupiah($data->accessories->price)}},-</a>
                            </td>
                            <td class="text-center">{{$data->qty}}</td>
                        </tr>
                    @endforeach
                    @else
                        @foreach($accesout as $key => $acces)
                            <tr>
                                <td class="text-center">{{tanggal($acces->acces_out)}}</td>
                                <td>{{$acces->accessories->code_acces}}</td>
                                <td>
                                    <a class="text-dark">{{$acces->accessories->name}}</a>
                                </td>
                                <td>
                                    <a class="text-dark">{{formatRupiah($acces->accessories->price)}},-</a>
                                </td>
                                <td class="text-center">{{$acces->qty}}</td>
                            </tr>
                        @endforeach
                    @endif
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
