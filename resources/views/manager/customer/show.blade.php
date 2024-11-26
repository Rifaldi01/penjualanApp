@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row mt-2">
                <div class="col-6">
                    <div class="container">
                        <h4 class=" text-uppercase">{{$customer->name}} <i class="bx bx-history"></i></h4>
                    </div>
                </div>
                <div class="col-6">
                    <a href="{{route('manager.customer.index')}}" class="btn btn-warning float-end me-3 shadow">Back</a>
                </div>
            </div>
            <hr>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="excel" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="10%">Tanggal</th>
                        <th>Name</th>
                        <th>Item</th>
                        <th>Accessories</th>
                        <th width="2%">Total Item</th>
                        <th>Total Pay</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sale as $key => $data)
                    <tr>
                        <td>{{$key +1}}</td>
                        <td>{{dateId($data->created_at)}}</td>
                        <td>{{$data->customer->name}}</td>
                        <td>
                            @foreach($data->itemSales as $item)
                                <li>
                                    {{ $item->name }} ( {{ $item->itemCategory->name }} - {{$item->no_seri}} )
                                </li>
                            @endforeach
                        </td>
                        <td>
                            @foreach($data->accessoriesSales as $accessory)
                                <li>
                                    {{ $accessory->accessories->name }} - Qty: {{ $accessory->qty }}
                                </li>
                            @endforeach
                        </td>
                        <td class="text-center">{{$data->total_item}}</td>
                        <td>{{formatRupiah($data->pay)}}</td>
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
    <script>
        $(document).ready(function() {
            var table = $('#excel').DataTable( {
                lengthChange: false,
                buttons: [ 'excel']
            } );

            table.buttons().container()
                .appendTo( '#excel_wrapper .col-md-6:eq(0)' );
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        } );
    </script>
@endpush
