<!-- =========================================================
     JQUERY
========================================================= -->
<script src="{{ URL::to('assets/js/jquery.min.js') }}"></script>


<!-- =========================================================
     BOOTSTRAP
========================================================= -->
<script src="{{ URL::to('assets/js/bootstrap.bundle.min.js') }}"></script>


<!-- =========================================================
     SWEETALERT
========================================================= -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- =========================================================
     SIMPLEBAR
========================================================= -->
<script src="{{ URL::to('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>


<!-- =========================================================
     METISMENU
========================================================= -->
<script src="{{ URL::to('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>


<!-- =========================================================
     PERFECT SCROLLBAR
========================================================= -->
<script src="{{ URL::to('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>


<!-- =========================================================
     VECTOR MAP
========================================================= -->
<script src="{{ URL::to('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ URL::to('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>


<!-- =========================================================
     CHART
========================================================= -->
<script src="{{ URL::to('assets/plugins/chartjs/js/chart.js') }}"></script>


<!-- =========================================================
     DATATABLE
========================================================= -->
<script src="{{ URL::to('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::to('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>


<!-- =========================================================
     SELECT2
========================================================= -->
<script src="{{ URL::to('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::to('assets/plugins/select2/js/select2-custom.js') }}"></script>


<!-- =========================================================
     APP JS
========================================================= -->
<script src="{{ URL::to('assets/js/app.js') }}"></script>


<!-- =========================================================
     FLATPICKR
========================================================= -->
<script src="{{ URL::to('assets/js/flatpickr.min.js') }}"></script>


<!-- =========================================================
     FEATHER ICON
========================================================= -->
<script src="https://unpkg.com/feather-icons"></script>


<!-- =========================================================
     INITIALIZATION
========================================================= -->
<script>
    $(document).ready(function () {

        /* =========================================================
           SIDEBAR
           Khusus halaman transaksi baru:
           - otomatis collapsed
           - tombol toggle tetap bisa membuka kembali
        ========================================================= */




        /* =========================================================
           DATATABLE
           Gunakan pengecekan isDataTable agar tidak terjadi:

           Cannot reinitialise DataTable
        ========================================================= */

        function initDataTable(selector, options = {}) {

            if (!$(selector).length) {
                return null;
            }

            // Jika sudah pernah diinisialisasi,
            // jangan inisialisasi ulang
            if ($.fn.DataTable.isDataTable(selector)) {
                return $(selector).DataTable();
            }

            return $(selector).DataTable(options);
        }


        /* =========================================================
           EXAMPLE
        ========================================================= */

        initDataTable('#example');


        /* =========================================================
           EXAMPLE 2
        ========================================================= */

        let table2 = initDataTable('#example2', {

            lengthChange: false,

            buttons: [
                'copy',
                'excel',
                'pdf',
                'print'
            ]

        });

        if (table2) {

            table2.buttons()
                .container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');

        }


        /* =========================================================
           EXAMPLE 3
        ========================================================= */

        let table3 = initDataTable('#example3', {

            lengthChange: false,

            buttons: [
                'pdf',
                {
                    extend: 'print',

                    exportOptions: {
                        stripHtml: false,

                        columns: [
                            0,
                            1,
                            2,
                            3,
                            4
                        ]
                    }
                }
            ]

        });

        if (table3) {

            table3.buttons()
                .container()
                .appendTo('#example3_wrapper .col-md-6:eq(0)');

        }


        /* =========================================================
           EXAMPLE 4
        ========================================================= */

        let table4 = initDataTable('#example4', {

            lengthChange: false,

            buttons: [
                'pdf',
                {
                    extend: 'print',

                    exportOptions: {
                        stripHtml: false,

                        columns: [
                            0,
                            1,
                            2,
                            3,
                            4
                        ]
                    }
                }
            ]

        });

        if (table4) {

            table4.buttons()
                .container()
                .appendTo('#example4_wrapper .col-md-6:eq(0)');

        }


        /* =========================================================
           EXAMPLE 5
        ========================================================= */

        let table5 = initDataTable('#example5', {

            lengthChange: false,

            buttons: [
                'pdf',

                {
                    extend: 'print',

                    exportOptions: {
                        stripHtml: false
                    },

                    customize: function (win) {

                        $(win.document.body)
                            .find('table')
                            .addClass('compact')
                            .css('font-size', '10px');


                        // Clone body
                        let bodyContent = $('#example5 tbody').clone();

                        $(win.document.body)
                            .find('table')
                            .append(bodyContent);


                        // Clone footer
                        let footerContent = $('#example5 tfoot').clone();

                        $(win.document.body)
                            .find('table')
                            .append(footerContent);

                    }

                }

            ]

        });

        if (table5) {

            table5.buttons()
                .container()
                .appendTo('#example5_wrapper .col-md-6:eq(0)');

        }


        /* =========================================================
           POPOVER
        ========================================================= */

        $('[data-bs-toggle="popover"]').popover();


        /* =========================================================
           TOOLTIP
        ========================================================= */

        $('[data-bs-toggle="tooltip"]').tooltip();


        /* =========================================================
           FEATHER ICON
        ========================================================= */

        if (typeof feather !== 'undefined') {

            feather.replace();

        }


        /* =========================================================
           FLATPICKR
        ========================================================= */

        if (typeof flatpickr !== 'undefined') {

            $('.datepicker').flatpickr();


            $('.time-picker').flatpickr({

                enableTime: true,

                noCalendar: true,

                dateFormat: 'Y-m-d H:i'

            });


            $('.date-time').flatpickr({

                enableTime: true,

                dateFormat: 'Y-m-d H:i'

            });


            $('.date-format').flatpickr({

                altInput: true,

                altFormat: 'F j, Y',

                dateFormat: 'Y-m-d'

            });


            $('.date-range').flatpickr({

                mode: 'range',

                altInput: true,

                altFormat: 'F j, Y',

                dateFormat: 'Y-m-d'

            });


            $('.date-inline').flatpickr({

                inline: true,

                altInput: true,

                altFormat: 'F j, Y',

                dateFormat: 'Y-m-d'

            });

        }


        /* =========================================================
           SUBMIT BUTTON
           Hapus titik pada harga sebelum submit
        ========================================================= */

        $(document).off('click.submitBtn', '#submitBtn');

        $(document).on('click.submitBtn', '#submitBtn', function (event) {

            event.preventDefault();

            const $button = $(this);

            $button
                .prop('disabled', true)
                .text('Memuat...');


            let priceInput = $('input[name="price"]');

            let priceInput2 = $('input[name="capital_price"]');

            let priceInput3 = $('input[name="price_bottom"]');


            if (priceInput.length) {

                priceInput.val(
                    priceInput.val().replace(/\./g, '')
                );

            }


            if (priceInput2.length) {

                priceInput2.val(
                    priceInput2.val().replace(/\./g, '')
                );

            }


            if (priceInput3.length) {

                priceInput3.val(
                    priceInput3.val().replace(/\./g, '')
                );

            }


            $('#myForm').submit();

        });


        /* =========================================================
           TOOLTIP CUSTOM
        ========================================================= */

        let tooltipTriggerList =
            [].slice.call(
                document.querySelectorAll('[data-bs-tool="tooltip"]')
            );


        tooltipTriggerList.map(function (tooltipTriggerEl) {

            return new bootstrap.Tooltip(tooltipTriggerEl);

        });

    });
</script>


<!-- =========================================================
     FORMAT RUPIAH
========================================================= -->
<script>

    function formatRupiah(element) {

        let value = element.value.replace(/[^,\d]/g, '');

        let split = value.split(',');

        let sisa = split[0].length % 3;

        let rupiah = split[0].substr(0, sisa);

        let ribuan = split[0]
            .substr(sisa)
            .match(/\d{3}/gi);


        if (ribuan) {

            let separator = sisa ? '.' : '';

            rupiah += separator + ribuan.join('.');

        }


        rupiah =
            split[1] !== undefined
                ? rupiah + ',' + split[1]
                : rupiah;


        element.value = rupiah;

    }

</script>


<!-- =========================================================
     PUSHER
========================================================= -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>

    Pusher.logToConsole = true;


    var pusher = new Pusher(
        '5af8b56c9ba705ddfb37',
        {
            cluster: 'ap1'
        }
    );


    // Subscribe sesuai divisi user login
    var channel = pusher.subscribe(
        'sidebar.{{ Auth::user()->divisi_id }}'
    );


    // Berhasil subscribe
    channel.bind(
        'pusher:subscription_succeeded',
        function () {

            console.log(
                'Pusher sidebar berhasil terhubung'
            );

        }
    );


    // Event dari Laravel
    channel.bind(
        'sidebar.updated',
        function (data) {

            console.log('EVENT MASUK');

            console.log(data);


            updateBadge(
                'badge-notif',
                data.notif
            );


            updateBadge(
                'badge-minta',
                data.minta
            );


            updateBadge(
                'badge-notiff',
                data.notiff
            );


            updateBadge(
                'badge-notifretur',
                data.notifretur
            );


            updateBadge(
                'badge-notifitem',
                data.notifitem
            );


            updateBadge(
                'badge-mintaitem',
                data.mintaitem
            );


            updateBadge(
                'badge-notiffitem',
                data.notiffitem
            );


            updateBadge(
                'badge-notifreturitem',
                data.notifreturitem
            );

        }
    );


    function updateBadge(id, total) {

        const badge =
            document.getElementById(id);


        if (!badge) {

            return;

        }


        const count =
            document.getElementById(
                id + '-count'
            );


        total = parseInt(total) || 0;


        if (total > 0) {

            if (count) {

                count.textContent = total;

            }


            badge.style.display =
                'inline-block';

        } else {

            badge.style.display =
                'none';

        }

    }

</script>
<script>
    $(document).ready(function () {

        const isSaleCreate = @json(
        request()->routeIs('admin.sale.create') ||
        request()->routeIs('manager.sale.create')
    );

        const $wrapper = $('.wrapper');
        const $sidebar = $('.sidebar-wrapper');

        if (!isSaleCreate || !$sidebar.length) {
            return;
        }

        /* =========================================================
           AKTIFKAN MODE HOVER KHUSUS TRANSAKSI
        ========================================================= */

        $wrapper.addClass('sale-create-hover');


        /* =========================================================
           SIDEBAR AWALNYA KECIL
        ========================================================= */

        $wrapper.addClass('toggled');


        /* =========================================================
           MOUSE MASUK SIDEBAR
           Sidebar menjadi besar
        ========================================================= */

        $sidebar.on('mouseenter', function () {

            $wrapper.addClass('sidebar-hover');

            $wrapper.removeClass('toggled');

        });


        /* =========================================================
           MOUSE KELUAR SIDEBAR
           Sidebar kembali kecil
        ========================================================= */

        $sidebar.on('mouseleave', function () {

            $wrapper.removeClass('sidebar-hover');

            $wrapper.addClass('toggled');

        });

    });
</script>

<!-- =========================================================
     PAGE SPECIFIC JS
========================================================= -->

@stack('js')
