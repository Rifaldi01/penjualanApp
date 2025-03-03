@extends('layouts.master')
@section('content')
   <div class="card col-12">
       <div class="card-body col-12">
           <div class="text-center mt-3 mb-3">
               <h2>Selamat Datang, {{Auth::user()->name}}</h2>
               <h3>Anda Masuk Sebagai Admin</h3>
               <a href="{{route('admin.sale.create')}}" class="btn btn-dnd mt-5">New Transaction</a>
           </div>
       </div>
   </div>
   <div class="card col-12">
       <div class="card-body col-12">
           <div class="text-center mt-3 mb-3">
               <h3>Pembayaran Mendekati Jatuh Tempo</h3>
           </div>
           <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered" style="width:100%">
                   <thead>
                   <tr>
                       <th width="4%">No</th>
                       <th class="text-center" width="5%">Deadlines</th>
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
                               <td>{{dateId($data->deadlines)}}</td>
                               <td>{{$data->customer->name}}</td>
                               <td class="text-center">{{$data->total_item}}</td>
                               <td>{{formatRupiah($data->total_price)}}</td>
                               <td>{{formatRupiah($data->diskon)}}</td>
                               <td>{{formatRupiah($data->ongkir)}}</td>
                               <td>{{formatRupiah($data->pay)}}</td>
                               <td>{{$data->user->name}}</td>
                               <td>
                                   <a href="https://api.whatsapp.com/send?phone=62{{ $data->customer->phone_wa}}&text=Halo%20Customer%20yth,%20segera%20selesaikan%20tagihan%20Pembelian-mu%20yang%20akan%20jatuh%20tempo%20pada%20{{dateId($data->deadlines)}}"
                                      class="btn btn-success lni lni-whatsapp float-end me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Chat Customer" target="_blank" rel="noopener noreferrer">
                                   </a>
                               </td>
                           </tr>
                       @endif
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
@endpush
