<!doctype html>
<html lang="en">

@include('layouts.component.head')
<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 31 Jul 2023 15:36:57 GMT -->


<div class="wrapper">

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

    @include('layouts.component.header')

    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <div class="overlay toggle-icon"></div>

    <a href="javaScript:;" class="back-to-top">
        <i class='bx bxs-up-arrow-alt'></i>
    </a>

    @include('layouts.component.footer')

</div>

@include('layouts.component.js')<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 31 Jul 2023 15:39:19 GMT -->
</html>
@include('sweetalert::alert')

{{--@include('errors.maintenance')--}}


