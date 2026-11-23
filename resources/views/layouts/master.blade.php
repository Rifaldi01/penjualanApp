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
            {{-- Popup Warning Hosting --}}
{{--            <div class="modal fade" id="hostingWarningModal" tabindex="-1"--}}
{{--                 aria-labelledby="hostingWarningModalLabel" aria-hidden="true"--}}
{{--                 data-bs-backdrop="static">--}}

{{--                <div class="modal-dialog modal-dialog-centered">--}}
{{--                    <div class="modal-content border-0 shadow">--}}

{{--                        <div class="modal-header bg-warning">--}}
{{--                            <h5 class="modal-title text-dark" id="hostingWarningModalLabel">--}}
{{--                                <i class="bx bx-info-circle me-2"></i>--}}
{{--                                Warning Hosting--}}
{{--                            </h5>--}}

{{--                            <button type="button" class="btn-close"--}}
{{--                                    data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                        </div>--}}

{{--                        <div class="modal-body text-center py-4">--}}

{{--                            <div class="mb-3">--}}
{{--                                <i class="bx bx-error-circle text-warning"--}}
{{--                                   style="font-size: 65px;"></i>--}}
{{--                            </div>--}}

{{--                            <h5 class="fw-bold mb-3">--}}
{{--                                Pemberitahuan Masa Aktif Hosting & Domain--}}
{{--                            </h5>--}}

{{--                            <p class="mb-0 text-secondary">--}}
{{--                                Kami ingin mengingatkan bahwa hosting & Domain--}}
{{--                                <strong>penjualan.dnd-survey.com</strong> Anda akan berakhir pada:--}}
{{--                            </p>--}}

{{--                            <h4 class="text-danger fw-bold mt-3">--}}
{{--                                25 November 2026--}}
{{--                            </h4>--}}

{{--                            <p class="text-muted small mt-3 mb-0">--}}
{{--                                Mohon segera lakukan perpanjangan hosting--}}
{{--                                agar layanan tetap berjalan dengan normal.--}}
{{--                            </p>--}}

{{--                        </div>--}}

{{--                        <div class="modal-footer justify-content-center">--}}
{{--                            <button type="button" class="btn btn-warning px-4"--}}
{{--                                    data-bs-dismiss="modal">--}}
{{--                                 Ok--}}
{{--                            </button>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
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


