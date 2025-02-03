@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Account</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a href="{{route('superadmin.account.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"
                    ><i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Divisi</th>
                        <th>Role</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="9%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($user as $key => $data)
                            @foreach($data->roles as $role)
                                @if($data->roles == !null)
                                    <td>{{$key +1}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->email}}</td>
                                    <td>
                                        @if($data->divisi)
                                            {{$data->divisi->name}}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{$role->name}}</td>
                                    <td class="text-center">
                                        @if($data->isOnline())
                                            <span class="badge bg-success">Online</span>
                                        @else
                                            <span class="badge bg-danger">Offline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('superadmin.account.destroy', $data->id)}}"
                                           data-confirm-delete="true"
                                           class="btn btn-danger btn-sm bx bx-trash" title="Delete">
                                        </a>
                                        <a href="{{route('superadmin.account.edit', $data->id)}}"
                                           class="btn btn-sm btn-warning bx bx-edit "
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></a>
                                    </td>
                    </tr>
                    @endif
                    @endforeach
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
