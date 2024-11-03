<!-- Bootstrap JS -->
<script src="{{URL::to('assets/js/bootstrap.bundle.min.js')}}"></script>
<!--plugins-->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{URL::to('assets/js/jquery.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
<script src="{{URL::to('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<script src="{{URL::to('assets/plugins/chartjs/js/chart.js')}}"></script>
<script src="{{URL::to('assets/js/index.js')}}"></script>
<script src="{{URL::to('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::to('assets/plugins/select2/js/select2-custom.js')}}"></script>
<!--app JS-->
<script src="{{URL::to('assets/js/app.js')}}"></script>
<script>
	new PerfectScrollbar(".app-container")
</script>
<script>
	$(document).ready(function() {
		$('#example').DataTable();
	  } );
</script>
<script>
	$(document).ready(function() {
		var table = $('#example2').DataTable( {
			lengthChange: false,
			buttons: [ 'copy', 'excel', 'pdf', 'print']
		} );

		table.buttons().container()
			.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
	} );
</script>
<script>
	$(document).ready(function() {
		var table = $('#example3').DataTable( {
			lengthChange: false,
			buttons: [ 'pdf', {
                extend: 'print',
                exportOptions: {
                    stripHtml : false,
                    columns: [0, 1, 2, 3, 4]
                }
            }]
		} );

		table.buttons().container()
			.appendTo( '#example3_wrapper .col-md-6:eq(0)' );
	} );
</script>
<script>
	$(document).ready(function() {
		var table = $('#example4').DataTable( {
			lengthChange: false,
			buttons: [ 'pdf', {
                extend: 'print',
                exportOptions: {
                    stripHtml : false,
                    columns: [0, 1, 2, 3, 4]
                }
            }]
		} );

		table.buttons().container()
			.appendTo( '#example4_wrapper .col-md-6:eq(0)' );
	} );
</script>
<script>
    $(document).ready(function() {
        var table = $('#example5').DataTable({
            lengthChange: false,
            buttons: [
                'pdf',
                {
                    extend: 'print',
                    exportOptions: {
                        stripHtml: false,
                    },
                    customize: function(win) {
                        $(win.document.body).find('table').addClass('compact').css('font-size', '10px');

                        // Append the <tfoot> content manually
                        var bodyContent = $('#example5 tbody').clone();
                        $(win.document.body).find('table').append(bodyContent);
                        var footerContent = $('#example5 tfoot').clone();
                        $(win.document.body).find('table').append(footerContent);
                    }
                }
            ]
        });

        table.buttons().container()
            .appendTo('#example5_wrapper .col-md-6:eq(0)');
    });
</script>
<script>
	$(function () {
		$('[data-bs-toggle="popover"]').popover();
		$('[data-bs-toggle="tooltip"]').tooltip();
	})
</script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace()
</script>
<script>
    $(document).ready(function() {
        $('#submitBtn').click(function(event) {
            // Nonaktifkan tombol dan ubah teksnya
            $(this).prop('disabled', true).text('Memuat...');

            // Hapus titik dari input harga
            let priceInput = $('input[name="price"]');
            let priceInput2 = $('input[name="capital_price"]');
            let priceValue = priceInput.val().replace(/\./g, '');
            priceInput.val(priceValue);
            let priceValue2 = priceInput2.val().replace(/\./g, '');
            priceInput2.val(priceValue2);

            // Kirim form secara manual
            $('#myForm').submit();
        });
    });

    function formatRupiah(element) {
        let value  = element.value.replace(/[^,\d]/g, '');
        let split  = value.split(',');
        let sisa   = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        element.value = rupiah;
    }
</script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-tool="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
<script src="{{URL::to('assets/js/flatpickr.min.js')}}"></script>
<script>
    $(".datepicker").flatpickr();
    $(".time-picker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "Y-m-d H:i",
    });
    $(".date-time").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    $(".date-format").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $(".date-range").flatpickr({
        mode: "range",
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $(".date-inline").flatpickr({
        inline: true,
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
</script>
@stack('js')
