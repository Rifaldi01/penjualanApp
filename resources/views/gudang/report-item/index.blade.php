@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Items Sale</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <!-- Tabel Data Item Terjual -->
                <table id="example5" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th>No Invoice</th>
                        <th>No Seri</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Pembeli</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($report as $key => $data)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{$data->sale->invoice}}</td>
                            <td>{{ $data->itemCategory->name }}-{{ $data->no_seri }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->itemCategory->name }}</td>
                            <td>{{ formatRupiah($data->price) }}</td>
                            <td>{{ $data->sale->customer->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="7" class="text-center bg-success border-bottom">SERING TERJUAL</th>
                    </tr>
                    @foreach ($mostSoldCategories as $category => $count)
                        <tr>
                            <th colspan="4" class="text-center border-bottom">{{ $category }}</th>
                            <th colspan="3" class="text-center border-bottom">{{ $count }} kali</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="7" class="text-center bg-warning border-bottom">JARANG TERJUAL</th>
                    </tr>
                    @foreach ($leastSoldCategories as $category => $count)
                        <tr>
                            <th colspan="4" class="text-center border-bottom">{{ $category }}</th>
                            <th colspan="3" class="text-center border-bottom">{{ $count }} kali</th>
                        </tr>
                    @endforeach
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')

@endpush
