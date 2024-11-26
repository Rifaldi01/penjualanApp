<!doctype html>
<html lang="en">

@include('layouts.component.head')
<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 31 Jul 2023 15:36:57 GMT -->


<body>
<!--wrapper-->
<div class="wrapper">
    <!--sidebar wrapper -->
    @role('gudang')
    @include('layouts.component.sidebar.sidebar')
    @endrole
    @role('superAdmin')
    @include('layouts.component.sidebar.sidebar-superAdmin')
    @endrole
    @role('admin')
    @include('layouts.component.sidebar.sidebar-admin')
    @endrole
    @role('manager')
    @include('layouts.component.sidebar.sidebar-manager')
    @endrole
    <!--end sidebar wrapper -->
    <!--start header -->
    @include('layouts.component.header')
    <!--end header -->
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>
    <!--end page wrapper -->
    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    @include('layouts.component.footer')
</div>
<!--end wrapper-->


<!-- search modal -->
<!-- end search modal -->

@include('layouts.component.js')
</body>


<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 31 Jul 2023 15:39:19 GMT -->
</html>
@include('sweetalert::alert')

