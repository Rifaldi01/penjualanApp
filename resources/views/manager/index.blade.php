@extends('layouts.master')
@section('content')
    <h3>Divisi</h3>
    <hr>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Divisi</p>
                            <h4 class="my-1 text-primary">{{$totaldivisi}}</h4>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-deepblue text-white ms-auto"><i
                                class='lni lni-apartment'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($divisi as $data)
            <div class="col">
                <div class="card radius-10 border-start border-0 border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{$data->name}}</p>

                                    @if($data->status == 0)
                                    <h4 class="my-1 text-primary">
                                        <i class="bx bx-check-circle"></i>
                                    </h4>
                                    @else
                                    <h4 class="my-1 text-danger">
                                        <i class="bx bx-x-circle"></i>
                                    </h4>
                                    @endif
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-deepblue text-white ms-auto"><i
                                    class='lni lni-apartment'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <hr>
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
    <hr>
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">Activity Account</h4>
                    </div>
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
