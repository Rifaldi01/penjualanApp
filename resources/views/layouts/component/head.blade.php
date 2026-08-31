<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="{{URL::to('assets/images/asd.png')}}" type="image/png')}}"/>
	<!--plugins-->
	<link href="{{URL::to('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')}}" rel="stylesheet"/>
	<link href="{{URL::to('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
	<link href="{{URL::to('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
	<link href="{{URL::to('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet"/>
	<link href="{{URL::to('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    <link href="{{URL::to('assets/css/flatpickr.min.css')}}" rel="stylesheet"/>
    <!-- loader-->
	<link href="{{URL::to('assets/css/pace.min.css')}}" rel="stylesheet"/>
	<script src="{{URL::to('assets/js/pace.min.js')}}"></script>
	<!-- Bootstrap CSS -->
	<link href="{{URL::to('assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{URL::to('assets/css/bootstrap-extended.css')}}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
	<link href="{{URL::to('assets/css/app.css')}}" rel="stylesheet">
	<link href="{{URL::to('assets/css/icons.css')}}" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="{{URL::to('assets/css/dark-theme.css')}}"/>
	<link rel="stylesheet" href="{{URL::to('assets/css/semi-dark.css')}}"/>
	<link rel="stylesheet" href="{{URL::to('assets/css/header-colors.css')}}"/>
    <link rel="stylesheet" href="{{URL::to('assets/css/select2.min.css')}}" />
    <link rel="stylesheet" href="{{URL::to('assets/css/select2-bootstrap-5-theme.min.css')}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penjualan App</title>
    <style>
        <style>
            /* =========================================================
               SALE CREATE SIDEBAR HOVER
            ========================================================= */

        @media (min-width: 769px) {

            .wrapper.sale-create-hover .sidebar-wrapper {
                width: 70px;
                transition: width 0.2s ease;
            }

            .wrapper.sale-create-hover .page-wrapper {
                margin-left: 70px;
                transition: margin-left 0.2s ease;
            }

            /* Saat kursor masuk sidebar */
            .wrapper.sale-create-hover.sidebar-hover .sidebar-wrapper {
                width: 250px;
            }

            .wrapper.sale-create-hover.sidebar-hover .page-wrapper {
                margin-left: 250px;
            }

        }
    </style>
    </style>
</head>
@stack('head')
