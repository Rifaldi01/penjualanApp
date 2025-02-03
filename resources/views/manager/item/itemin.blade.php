@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        @if(request()->routeIs('manager.item.itemin'))
                        <h4 class="text-uppercase">List Items IN</h4>
                        @else
                        <h4 class="text-uppercase">List Items OUT</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example3" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th class="text-center" width="15%">Tanggal</th>
                        <th width="15%">No Seri</th>
                        <th width="15%">Divisi</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Modal</th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(request()->routeIs('manager.item.itemin'))
                        @foreach($itemin as $item)
                            <tr>
                                <td class="text-center">
                                    {{-- Cek apakah objek item adalah instance dari ItemSale --}}
                                    @if($item instanceof \App\Models\ItemSale)
                                        {{ tanggal($item->date_in) }}
                                    @elseif($item instanceof \App\Models\Item)
                                        {{ tanggal($item->created_at) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ isset($item->itemCategory) ? $item->itemCategory->name : $item->cat->name }}
                                    -{{ $item->no_seri }}
                                </td>
                                <td>
                                    {{ $item->divisi->name }}
                                </td>
                                <td>
                                    <a class="text-dark">{{ $item->name }}</a>
                                </td>
                                <td>
                                    {{ isset($item->itemCategory) ? $item->itemCategory->name : $item->cat->name }}
                                </td>
                                <td>{{ formatRupiah($item->capital_price) }}</td>
                                <td>{{ formatRupiah($item->price) }}</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach($itemout as $item)
                            <tr>
                                <td class="text-center">{{ tanggal($item->created_at) }}</td>
                                <td>{{ $item->itemCategory->name}}-{{ $item->no_seri }}</td>
                                <td>
                                    {{ $item->divisi->name }}
                                </td>
                                <td><a class="text-dark">{{ $item->name }}</a></td>
                                <td>{{$item->itemCategory->name}}</td>
                                <td>{{ formatRupiah($item->capital_price) }}</td>
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
