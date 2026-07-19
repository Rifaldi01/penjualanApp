@extends('layouts.master')
@if(request()->routeIs('gudang.item.itemin'))
    @section('title', 'DAFTAR ALAT MASUK')
@else
    @section('title', 'DAFTAR ALAT KELUAR')
@endif
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">

                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example3" class="table table-striped table-bordered" style="width:100%">
                    @if(request()->routeIs('gudang.item.itemin'))
                    <thead>
                    <tr>
                        <th class="text-center" width="15%">Tanggal</th>
                        <th width="15%">No Seri</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($itemin as $item)
                            <tr>
                                <td class="text-center">
                                    {{ tanggal($item->created_at) }}
                                </td>
                                <td>
                                    {{ isset($item->itemCategory) ? $item->itemCategory->name : $item->cat->name }}
                                    -{{ $item->no_seri }}
                                </td>
                                <td>
                                    <a class="text-dark">{{ $item->name }}</a>
                                </td>
                                <td>
                                    {{ isset($item->itemCategory) ? $item->itemCategory->name : $item->cat->name }}
                                </td>
                                <td>{{ formatRupiah($item->price) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th class="text-center" width="15%">Tanggal</th>
                            <th width="15%">No Seri</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                        </tr>
                        </thead>
                        @foreach($itemout as $item)
                            <tr>
                                <td>{{$item->sale->invoice}}</td>
                                <td class="text-center">{{ tanggal($item->created_at) }}</td>
                                <td>{{ $item->itemCategory->name}}-{{ $item->no_seri }}</td>
                                <td><a class="text-dark">{{ $item->name }}</a></td>
                                <td>{{$item->itemCategory->name}}</td>
                                <td>{{ formatRupiah($item->price) }}</td>
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
