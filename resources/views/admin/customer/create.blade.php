@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="row">
            <div class="col-sm-6 mt-3 ">
                @if(isset($cust))
                    <h5 class="mb-4 ms-3">Edit Customer<i class="bx bx-edit"></i></h5>
                @else
                    <h5 class="mb-4 ms-3">Register Customer<i class="bx bx-user-plus"></i></h5>
                @endif
            </div>
            <div class="col-sm-6 mt-3">
                @if(isset($cust))
                    <a href="{{route('admin.customer.index')}}" class="btn btn-warning btn-sm float-end me-3"><i class="bx bx-arrow-back"></i> Back</a>
                @else
                @endif
            </div>
        </div>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-danger"><i class='bx bxs-message-square-x'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-danger">Error</h6>
                            <div>
                                <div>{{ $error }}</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        @endif
        <form class="card-body p-4" action="{{$url}}" method="POST" enctype="multipart/form-data" id="myForm">
            @csrf
            @isset($cust)
                @method('PUT')
            @endif
            <div class="row mb-3">
                <label for="input42" class="col-sm-3 col-form-label"><i class="text-danger">*</i> Customer Name</label>
                <div class="col-sm-9">
                    <div class="position-relative input-icon">
                        <input type="text" name="name" class="form-control" id="input42"
                               placeholder="Enter Csutomer Name" value="{{isset($cust) ? $cust->name : null}}">
                        <span class="position-absolute top-50 translate-middle-y"><i class='bx bx-user'></i></span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="input43" class="col-sm-3 col-form-label"><i class="text-danger">*</i> Phone Whatsapp</label>
                <div class="col-sm-9">
                    <div class="position-relative input-icon">
                        <input type="number" name="phone_wa" class="form-control" id="input43" placeholder="81XXXXXXXXXX"
                               value="{{isset($cust) ? $cust->phone_wa : null}}">
                        <span class="position-absolute top-50 translate-middle-y"><i class='bx bxl-whatsapp'></i></span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="input43" class="col-sm-3 col-form-label">&nbsp;&nbsp; Phone </label>
                <div class="col-sm-9">
                    <div class="position-relative input-icon">
                        <input type="number" name="phone" class="form-control" id="input43" placeholder="(optional)"
                               value="{{isset($cust) ? $cust->phone : null}}">
                        <span class="position-absolute top-50 translate-middle-y"><i class='bx bx-phone'></i></span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="input47" class="col-sm-3 col-form-label">&nbsp;&nbsp;Company</label>
                <div class="col-sm-9">
                    <div class="position-relative input-icon">
                        <input class="form-control" name="company" id="input47" placeholder="(Optional)"
                               value="{{isset($cust) ? $cust->company : null}}">
                        <span class="position-absolute top-50 translate-middle-y"><i class='bx bx-building'></i></span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="input47" class="col-sm-3 col-form-label"><i class="text-danger">*</i> Address</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="addres" id="input47" rows="3" placeholder="Address"
                              value="">{{isset($cust) ? $cust->addres : null}}</textarea>
                </div>
            </div>
            <div class="d-md-flex d-grid align-items-center gap-3 float-end">
                <button type="submit" class="btn btn-dnd px-4" id="btn">Save <i class="bx bx-save me-0"></i></button>
            </div>
        </form>
    </div>
@endsection

@push('head')
@endpush
@push('js')
    <script>
        $(document).ready(function() {
            $('#btn').click(function() {
                // Disable button dan ubah teksnya
                $(this).prop('disabled', true).text('Loading...');

                // Kirim form secara manual
                $('#myForm').submit();
            });
        });
    </script>
@endpush
