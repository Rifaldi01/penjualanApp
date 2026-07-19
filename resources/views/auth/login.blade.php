{{--<!doctype html>--}}
{{--<html lang="en">--}}


{{--<!-- Mirrored from codervent.com/rocker/demo/vertical/auth-basic-signin.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 May 2024 06:28:07 GMT -->--}}
{{--<head>--}}
{{--    <!-- Required meta tags -->--}}
{{--    <meta charset="utf-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}
{{--    <!--favicon-->--}}
{{--    <link rel="icon" href="{{URL::to('assets/images/asd.png')}}" type="image/png')}}"/>--}}
{{--    <!--plugins-->--}}
{{--    <link href="{{URL::to('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet"/>--}}
{{--    <link href="{{URL::to('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet"/>--}}
{{--    <link href="{{URL::to('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet"/>--}}
{{--    <!-- loader-->--}}
{{--    <link href="{{URL::to('assets/css/pace.min.css')}}" rel="stylesheet"/>--}}
{{--    <script src="{{URL::to('assets/js/pace.min.js')}}"></script>--}}
{{--    <!-- Bootstrap CSS -->--}}
{{--    <link href="{{URL::to('assets/css/bootstrap.min.css')}}" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/bootstrap-extended.css')}}" rel="stylesheet">--}}
{{--    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/app.css')}}" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/icons.css')}}" rel="stylesheet">--}}
{{--    <title>DNDSURVEY - LOGIN</title>--}}
{{--</head>--}}

{{--<body class="">--}}
{{--<!--wrapper-->--}}
{{--<div class="wrapper">--}}
{{--    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">--}}
{{--        <div class="container">--}}
{{--            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">--}}
{{--                <div class="col mx-auto">--}}
{{--                    <div class="card mb-0">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="p-4">--}}
{{--                                <div class="mb-3 text-center">--}}
{{--                                    <img src="{{URL::to('images/pos.png')}}" width="60%" alt=""/>--}}
{{--                                    <p class="mb-0">Please log in to your account</p>--}}
{{--                                </div>--}}
{{--                                <div class="form-body">--}}
{{--                                    <form class="row g-3" method="POST" action="{{ route('login') }}">--}}
{{--                                        @csrf--}}
{{--                                        <div class="col-12">--}}
{{--                                            <label for="inputEmailAddress" class="form-label">Email</label>--}}
{{--                                            <input type="email"--}}
{{--                                                   class="form-control @error('email') is-invalid @enderror"--}}
{{--                                                   name="email" value="{{ old('email') }}" required autocomplete="email"--}}
{{--                                                   autofocus id="email" placeholder="Enter Email">--}}
{{--                                            @error('email')--}}
{{--                                            <span class="invalid-feedback" role="alert">--}}
{{--                                                <strong>{{ $message }}</strong>--}}
{{--                                            </span>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                        <div class="col-12">--}}
{{--                                            <label for="inputChoosePassword" class="form-label">Password</label>--}}
{{--                                            <div class="input-group" id="show_hide_password">--}}
{{--                                                <input id="password" type="password"--}}
{{--                                                       class="form-control border-end-0 @error('password') is-invalid @enderror"--}}
{{--                                                       name="password" placeholder="Enter Password"--}}
{{--                                                       required autocomplete="current-password">--}}
{{--                                                <a href="javascript:;" class="input-group-text bg-transparent"><i--}}
{{--                                                        class='bx bx-hide'></i></a>--}}
{{--                                                @error('password')--}}
{{--                                                <span class="invalid-feedback" role="alert">--}}
{{--                                                    <strong>{{ $message }}</strong>--}}
{{--                                                 </span>--}}
{{--                                                @enderror--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-md-6">--}}
{{--                                            <div class="form-check form-switch">--}}
{{--                                                <input class="form-check-input" type="checkbox" name="remember"--}}
{{--                                                       id="remember" {{ old('remember') ? 'checked' : '' }}>--}}
{{--                                                <label class="form-check-label" for="flexSwitchCheckChecked">Remember--}}
{{--                                                    Me</label>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-12">--}}
{{--                                            <div class="d-grid">--}}
{{--                                                <button type="submit" class="btn btn-dnd">Sign in</button>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </form>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!--end row-->--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!--end wrapper-->--}}
{{--<!-- Bootstrap JS -->--}}
{{--<script src="{{URL::to('assets/js/bootstrap.bundle.min.js')}}"></script>--}}
{{--<!--plugins-->--}}
{{--<script src="{{URL::to('assets/js/jquery.min.js')}}"></script>--}}
{{--<script src="{{URL::to('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>--}}
{{--<script src="{{URL::to('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>--}}
{{--<script src="{{URL::to('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>--}}
{{--<!--Password show & hide js -->--}}
{{--<script>--}}
{{--    $(document).ready(function () {--}}
{{--        $("#show_hide_password a").on('click', function (event) {--}}
{{--            event.preventDefault();--}}
{{--            if ($('#show_hide_password input').attr("type") == "text") {--}}
{{--                $('#show_hide_password input').attr('type', 'password');--}}
{{--                $('#show_hide_password i').addClass("bx-hide");--}}
{{--                $('#show_hide_password i').removeClass("bx-show");--}}
{{--            } else if ($('#show_hide_password input').attr("type") == "password") {--}}
{{--                $('#show_hide_password input').attr('type', 'text');--}}
{{--                $('#show_hide_password i').removeClass("bx-hide");--}}
{{--                $('#show_hide_password i').addClass("bx-show");--}}
{{--            }--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}
{{--<!--app JS-->--}}
{{--<script src="{{URL::to('assets/js/app.js')}}"></script>--}}
{{--</body>--}}


{{--</html>--}}

{{--error page--}}
{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}


{{--<!-- Mirrored from codervent.com/rocker/demo/vertical/errors-500-error.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 May 2024 06:28:17 GMT -->--}}
{{--<head>--}}
{{--    <!-- Required meta tags -->--}}
{{--    <meta charset="utf-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}
{{--    <!--favicon-->--}}
{{--    <link rel="icon" href="{{URL::to('assets/images/asd.png')}}"  type="image/png" />--}}
{{--    <!-- loader-->--}}
{{--    <link href="{{URL::to('assets/css/pace.min.css')}}" rel="stylesheet" />--}}
{{--    <script src="{{URL::to('assets/js/pace.min.js')}}"></script>--}}
{{--    <!-- Bootstrap CSS -->--}}
{{--    <link href="{{URL::to('assets/css/bootstrap.min.css')}}" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/bootstrap-extended.css')}}" rel="stylesheet">--}}
{{--    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/app.css')}}" rel="stylesheet">--}}
{{--    <link href="{{URL::to('assets/css/icons.css')}}" rel="stylesheet">--}}
{{--    <title>Error Pages</title>--}}
{{--</head>--}}

{{--<body>--}}
{{--<!-- wrapper -->--}}
{{--<div class="wrapper">--}}
{{--    <div class="error-404 d-flex align-items-center justify-content-center">--}}
{{--        <div class="container">--}}
{{--            <div class="card">--}}
{{--                <div class="row g-0">--}}
{{--                    <div class="col-xl-5">--}}
{{--                        <div class="card-body p-4">--}}
{{--                            <h1 class="display-1"><span class="text-warning">5</span><span class="text-danger">0</span><span class="text-primary">0</span></h1>--}}
{{--                            <h2 class="font-weight-bold display-4">Sorry, unexpected error</h2>--}}
{{--                            <p>Looks like you are lost!--}}
{{--                                <br>Your hosting period is finished, please renew it immediately!</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-xl-7">--}}
{{--                        <img src="{{URL::to('assets/images/errors-images/505-error.png')}}" class="img-fluid" alt="">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!--end row-->--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="bg-white p-3 fixed-bottom border-top shadow">--}}
{{--        <div class="d-flex align-items-center justify-content-between flex-wrap">--}}
{{--            <ul class="list-inline mb-0">--}}

{{--            </ul>--}}
{{--            <p class="mb-0">Copyright © 2024. DNDSURVEY.</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!-- end wrapper -->--}}
{{--<!-- Bootstrap JS -->--}}
{{--<script src="{{URL::to('assets/js/bootstrap.bundle.min.js')}}"></script>--}}
{{--</body>--}}


{{--<!-- Mirrored from codervent.com/rocker/demo/vertical/errors-500-error.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 May 2024 06:28:18 GMT -->--}}
{{--</html>--}}
    <!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Maintenance</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{URL::to('assets/images/asd.png')}}" type="image/png')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

    <style>

        body{

            margin:0;
            height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            background:linear-gradient(135deg,#1f2937,#0f172a,#111827);

            overflow:hidden;

            color:white;

            font-family:Segoe UI;

        }

        .card-maintenance{

            width:650px;

            border:none;

            border-radius:25px;

            background:rgba(255,255,255,.08);

            backdrop-filter:blur(15px);

            padding:50px;

            text-align:center;

            box-shadow:0 20px 60px rgba(0,0,0,.4);

            animation:fadeIn 1s;

        }

        .gear{

            font-size:90px;

            color:#ffc107;

            animation:spin 6s linear infinite;

        }

        h1{

            margin-top:20px;

            font-weight:bold;

        }

        p{

            color:#d7d7d7;

            margin-top:15px;

            font-size:18px;

        }

        .progress{

            height:15px;

            border-radius:30px;

            overflow:hidden;

            margin-top:40px;

        }

        .progress-bar{

            width:0;

            animation:loading 12s linear infinite;

        }

        .count{

            font-size:35px;

            font-weight:bold;

            color:#ffc107;

            margin-top:20px;

        }

        @keyframes spin{

            from{

                transform:rotate(0deg);

            }

            to{

                transform:rotate(360deg);

            }

        }

        @keyframes loading{

            from{

                width:0%;

            }

            to{

                width:100%;

            }

        }

        @keyframes fadeIn{

            from{

                opacity:0;

                transform:translateY(40px);

            }

            to{

                opacity:1;

                transform:translateY(0);

            }

        }

        .dot{

            position:absolute;

            width:10px;

            height:10px;

            background:white;

            border-radius:50%;

            opacity:.3;

            animation:float 8s linear infinite;

        }

        @keyframes float{

            from{

                transform:translateY(100vh);

            }

            to{

                transform:translateY(-100px);

            }

        }

    </style>

</head>

<body>

@for($i=0;$i<40;$i++)

    <div class="dot"
         style="
        left:{{ rand(1,100) }}%;
        animation-delay:{{ rand(1,8) }}s;
        animation-duration:{{ rand(5,15) }}s;
">
    </div>

@endfor


<div class="card-maintenance">

    <i class="fas fa-gears gear"></i>

    <h1>System Maintenance</h1>

    <p>

        Kami sedang melakukan peningkatan sistem agar memberikan pelayanan yang lebih baik.

    </p>

    <div class="progress mt-4">

        <div class="progress-bar bg-warning"></div>

    </div>

    <div class="count" id="countdown">
        😁
    </div>

    <small>

        Hormat kami, <a href="https://satyasoftware.id/">Satya Software Media</a>

    </small>

</div>

<script>



    setInterval(function(){

        second--;

        document.getElementById("countdown").innerHTML = second;



    },1000);

</script>

</body>
</html>
