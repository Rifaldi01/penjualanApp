@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Item</h4>
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
                        <th>Name</th>
                        <th>Stok</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($itemsByCategory as $key => $item)
                        <tr>
                            <th>{{$key +1}}</th>
                            <td><a href="{{route('admin.item.show', $item->cat->id)}}" class="text-dark">{{$item->cat->name}}</a></td>
                            <td>{{ $item->total }}</td>
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
