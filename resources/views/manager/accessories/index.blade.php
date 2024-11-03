@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Items</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Add Barcode"
                       href="{{route('manager.acces.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"><i
                            class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example4" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Name</th>
                        <th>Code Acces</th>
                        <th>Capital Price</th>
                        <th>Price</th>
                        <th class="text-center">Barcode</th>
                        <th class="text-center" width="10%">Stok</th>
                        <th class="text-center" width="10%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($acces as $key => $data)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>
                                <a class="text-dark">{{$data->name}}</a>
                            </td>
                            <td>{{$data->code_acces}}</td>
                            <td>
                               {{formatRupiah($data->capital_price)}},-
                            </td>
                            <td>
                                {{formatRupiah($data->price)}},-
                            </td>
                            <td class="text-center">
                                <a href="{{ route('acces.download', $data) }}" class="btn btn-white px-5">
                                    <img src="data:image/png;base64,{{$barcodes[$data->id] ?? ''}}" alt="Barcode for {{ $data->code_acces }}">
                                    <div>{{$data->code_acces}}</div>
                                </a>
                            </td>
                            <td class="text-center">{{$data->stok}}</td>
                            <td class="text-center">
                                <a href="{{route('manager.acces.destroy', $data->id)}}" data-confirm-delete="true"
                                   class="btn btn-danger btn-sm bx bx-trash" data-bs-toggle="tooltip"
                                   data-bs-placement="top" title="Delete">
                                </a>
                                <a href="{{route('manager.acces.edit', $data->id)}}"
                                   class="btn btn-sm btn-dnd bx bx-edit me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                </a>
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
