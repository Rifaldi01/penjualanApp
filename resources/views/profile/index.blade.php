@extends('layouts.master')
@section('content')
    <div class="main-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            @if(Auth::user()->image)
                                <img src="{{asset('images/profile/'. Auth::user()->image)}}" alt="Admin"
                                     class="rounded-circle p-1 shadow" width="110" >
                            @else
                                <img src="{{URL::to('images/avatar.png')}}" alt="Admin"
                                     class="rounded-circle p-1 bg-primary shadow" width="110">
                            @endif
                            <div class="mt-3">
                                <h4>{{Auth::user()->name}}</h4>
                                <p class="text-secondary mb-1 ">{{Auth::user()->email}}</p>
                                <p class="text-muted font-size-sm ">{{Auth::user()->phone}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
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
                    <form action="{{$url}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @isset($user)
                            @method('PUT')
                        @endisset
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="mt-2 mb-3">
                                    <h3>Edit Profile</h3>
                                </div>
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" class="form-control" name="name"
                                           value="{{isset($user) ? $user->name : null}}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" class="form-control" name="email"
                                           value="{{isset($user) ? $user->email : null}}"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" class="form-control" name="phone"
                                           value="{{isset($user) ? $user->phone : null}}"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Paswword</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="input-group" id="show_hide_password">
                                        <input id="password" type="password"
                                               class="form-control border-end-0"
                                               name="password" placeholder="Enter Password"
                                               autocomplete="current-password">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i
                                                class='bx bx-hide'></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Image Profile</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <div class="form-group">
                                        <div class="preview-zone hidden">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <div class="box-tools pull-right">
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm remove-preview">
                                                            <i class="text-light" data-feather="refresh-ccw"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="box-body mt-3 mb-3">
                                                    @if(isset($user))
                                                        <img src="{{asset('images/profile/'. $user->image )}}"
                                                             width="50%">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropzone-wrapper">
                                            <div class="dropzone-desc">
                                                <i class="glyphicon glyphicon-download-alt"></i>
                                                <p>Choose an image file or drag it here.</p>
                                            </div>
                                            <input type="file" name="image" accept="image/*" class="dropzone"
                                                   value="{{isset($user) ? $user->image : null}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-dnd px-4" value="Save Changes"/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" type="text/css" href="{{URL::to('assets/css/coba1.css')}}">
@endpush
@push('js')

    <script>
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var htmlPreview =
                        '<img width="200" src="' + e.target.result + '" />' +
                        '<p>' + input.files[0].name + '</p>';
                    var wrapperZone = $(input).parent();
                    var previewZone = $(input).parent().parent().find('.preview-zone');
                    var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

                    wrapperZone.removeClass('dragover');
                    previewZone.removeClass('hidden');
                    boxZone.empty();
                    boxZone.append(htmlPreview);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function reset(e) {
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap();
        }

        $(".dropzone").change(function () {
            readFile(this);
        });

        $('.dropzone-wrapper').on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        $('.dropzone-wrapper').on('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });

        $('.remove-preview').on('click', function () {
            var boxZone = $(this).parents('.preview-zone').find('.box-body');
            var previewZone = $(this).parents('.preview-zone');
            var dropzone = $(this).parents('.form-group').find('.dropzone');
            boxZone.empty();
            previewZone.addClass('hidden');
            reset(dropzone);
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
@endpush

