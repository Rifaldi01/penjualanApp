@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">Activity Account</h4>
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
                        <th>Role</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($user as $key => $data)
                            @foreach($data->roles as $role)
                                @if($data->roles == !null)
                                    <td>{{$key +1}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$role->name}}</td>
                                    <td class="text-center">
                                        @if($data->isOnline())
                                            <span class="badge bg-success">Online</span>
                                        @else
                                            <span class="badge bg-danger">Offline</span>
                                        @endif
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
