@extends('layouts.master')
@section('content')@if(Auth::user()->divisi_id == 6)
    <h3>List Stok Item</h3>
    <hr>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Items</p>
                            <h4 class="my-1 text-warning">{{$item}}</h4>
                            <p class="mb-0 font-13">All Items</p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class='bx bxs-box'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($itemsByCategory as $item)
            <div class="col">
                <div class="card radius-10 border-start border-0 border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{$item->cat->name}}</p>
                                <h4 class="my-1 text-warning">{{ $item->total }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                    class='bx bx-box'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <h3>List Stok Item</h3>
    <hr>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Items</p>
                            <h4 class="my-1 text-warning">{{$item}}</h4>
                            <p class="mb-0 font-13">All Items</p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class='bx bxs-box'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($itemsByCategory as $item)
            <div class="col">
                <div class="card radius-10 border-start border-0 border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{$item->cat->name}}</p>
                                <h4 class="my-1 text-warning">{{ $item->total }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                    class='bx bx-box'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
   <div class="card">
       <div class="card-head">
           <div class="container">
               <div class="mt-2">
                   <h3>TRANSAKSI TODAY</h3>
               </div>
           </div>
       </div>
       <div class="card-body">
           <div class="table-responsive">
               <table id="excel" class="table table-striped table-bordered" style="width:100%">
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
                           <tr>
                               <td data-index="{{ $key + 1 }}">{{$key +1}}</td>
                               <td>{{tanggal($data->created_at)}}</td>
                               <td>{{$data->invoice}}</td>
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
                                   @include('admin.sale.surat-jalan')
                                   <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                           data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                           data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                           data-bs-placement="top" title="Print Invoice">
                                   </button>
                                   @include('admin.sale.invoice')
                               </td>
                           </tr>
                   @endforeach
                   </tbody>
               </table>
           </div>
       </div>
   </div>
@endif
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
