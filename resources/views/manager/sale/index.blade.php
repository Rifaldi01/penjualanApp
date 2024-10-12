@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Trasaction Active</h4>
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
                    @foreach($sales as $key => $data)
                        @if($data->nominal_in == $data->pay)
                        @else
                            <tr>
                                <td data-index="{{ $key + 1 }}">{{$key +1}}</td>
                                <td>{{dateId($data->created_at)}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->diskon)}}</td>
                                <td>{{formatRupiah($data->ongkir)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('manager.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('manager.sale.invoice')
                                    <button class="btn btn-warning lni lni-dollar btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#examplemodal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Bayar"></button>
                                    <div class="modal fade" id="examplemodal{{$data->id}}" tabindex="-1"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pelunasan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <form action="{{route('manager.sale.bayar', $data->id)}}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="float-start col-lg-12">
                                                            <label for="" class="mb-2">
                                                                <strong>Nominal In</strong>
                                                            </label>
                                                            <input type="number" class="form-control mb-3"
                                                                   name="nominal_in"
                                                                   value="{{$data->pay}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary ">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{route('manager.sale.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm lni lni-pencil" data-bs-tool="tooltip"
                                       data-bs-placement="top" title="Edit Transaksi"></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                <table id="transaction" class="table table-striped table-bordered" style="width:100%">
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
                    @foreach($sales as $key => $data)
                        @if($data->nominal_in == $data->pay)
                            <tr>
                                <td data-index="{{ $key + 1 }}">{{$key +1}}</td>
                                <td>{{dateId($data->created_at)}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->diskon)}}</td>
                                <td>{{formatRupiah($data->ongkir)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('manager.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('manager.sale.invoice')
                                    <a href="{{route('manager.sale.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm lni lni-pencil" data-bs-tool="tooltip"
                                       data-bs-placement="top" title="Edit Transaksi"></a>
                                </td>
                            </tr>
                        @else
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head') @endpush
@push('js')
    <script>
        $(document).ready(function () {
            var table = $('#example').DataTable();

            // Mengurutkan ulang nomor saat tabel diurutkan atau difilter
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#transaction').DataTable();
        });
    </script>
    <script>
        $(document).ready(function () {
            var table = $('#transaction').DataTable();

            // Mengurutkan ulang nomor saat tabel diurutkan atau difilter
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        });
    </script>
@endpush

