@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Customer</h4>
                    </div>
                </div>
                <div class="col-6 mt-3">
                    <div class="container">
                        <a href="{{route('admin.customer.create')}}" class="btn btn-dnd btn-sm float-end me-2"><i
                                class="bx bx-plus" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Register Customer"></i></a>
                        <button type="button" class="btn btn-success btn-sm float-end me-1" data-bs-toggle="modal"
                                data-bs-target="#exampleModal" data-bs-tool="tooltip"
                                data-bs-placement="top" title="Import Data Excel"><i class="bx bx-file"></i>
                        </button>
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <form action="{{route('admin.importexcel')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <input class="form-control" type="file" name="file" accept=".xlsx,.xls,">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="submit" class="btn btn-primary">Save <i class="bx bx-save"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
                        <th>WhatsApp</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Company</th>
                        <th width="12%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cust as $key => $data)
                        <tr>
                            <th>{{$key +1}}</th>
                            <td><a href="{{route('admin.customer.show', $data->id)}}" class="text-dark">{{$data->name}}</a></td>
                            <td>0{{$data->phone_wa}}</td>
                            <td>
                                @if(isset($data->phone))
                                    0{{$data->phone}}
                                @else
                                    <div class="text-center">
                                        -
                                    </div>
                                @endif
                            </td>
                            <td>{{$data->addres}}</td>
                            <td>{{$data->company}}</td>
                            <td>
                                <a href="{{route('admin.customer.destroy', $data->id)}}" data-confirm-delete="true"
                                   type="submit" class=" bx bx-trash btn btn-sm btn-danger"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top" title="Hapus">
                                </a>
                                <a href="{{route('admin.customer.edit', $data->id)}}"
                                   class="btn btn-sm btn-warning bx bx-edit "
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
