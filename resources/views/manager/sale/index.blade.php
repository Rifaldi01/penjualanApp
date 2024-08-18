@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Trasaction</h4>
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
                        <th class="text-center" width="5%">Tanggal</th>
                        <th>Customer</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Total Price</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Total Pay</th>
                        <th width="5%">Kasir</th>
                        <th class="text-center" width="5%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sale as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{dateId($data->created_at)}}</td>
                            <td>{{$data->customer->name}}</td>
                            <td class="text-center">{{$data->total_item}}</td>
                            <td>{{formatRupiah($data->total_price)}}</td>
                            <td>{{formatRupiah($data->diskon)}}</td>
                            <td>{{formatRupiah($data->ongkir)}}</td>
                            <td>{{formatRupiah($data->pay)}}</td>
                            <td>{{$data->user->name}}</td>
                            <td class="text-center">
                                <button class="btn btn-dnd lni lni-eye btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                        data-bs-placement="top" title="View Detail"></button>
                                <div class="modal fade" id="exampleExtraLargeModal{{$data->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Transaction</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                            <tr>
                                                                <th colspan="6" class="bg-primary">ITEM</th>
                                                            </tr>
                                                            <tr>
                                                                <th width="4%">No</th>
                                                                <th>No Seri</th>
                                                                <th>Name</th>
                                                                <th>Category</th>
                                                                <th>Qty</th>
                                                                <th>Price</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($data->itemSales as $index => $itemSale)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $itemSale->no_seri }}</td>
                                                                    <td>{{ $itemSale->name }}</td>
                                                                    <td>{{ $itemSale->itemCategory->name }}</td>
                                                                    <td>1</td>
                                                                    <td>{{ formatRupiah($itemSale->price) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                            <tr>
                                                                <th colspan="6" class="bg-warning">Accessories</th>
                                                            </tr>
                                                            <tr>
                                                                <th width="4%">No</th>
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Price</th>
                                                                <th>Qty</th>
                                                                <th>Subtotal</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($data->accessoriesSales as $index => $accessoriesSale)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{$accessoriesSale->accessories->code_acces}}</td>
                                                                    <td>{{$accessoriesSale->accessories->name}}</td>
                                                                    <td>{{formatRupiah($accessoriesSale->accessories->price)}}</td>
                                                                    <td>{{$accessoriesSale->qty}}</td>
                                                                    <td>{{formatRupiah($accessoriesSale->subtotal)}}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
{{--                                <a href="{{route('manager.sale.edit', $data->id)}}" class="btn btn-warning lni lni-pencil btn-sm" data-bs-tool="tooltip"--}}
{{--                                   data-bs-placement="top" title="Edit"></a>--}}
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
