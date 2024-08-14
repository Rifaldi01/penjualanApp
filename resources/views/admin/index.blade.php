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
@endsection

@push('head')

@endpush
@push('js')

@endpush
