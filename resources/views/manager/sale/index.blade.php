@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Trasaction Active</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="excel" class="table table-striped table-bordered" style="width:50%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th class="text-center" >Tanggal</th>
                        <th>Divisi</th>
                        <th>INV</th>
                        <th>Customer</th>
                        <th class="text-center" >Total Item</th>
                        <th class="text-center">Ung Mask</th>
                        <th class="text-center" width="5%">Total Hrg</th>
                        <th class="text-center" width="5%">Bayar</th>
                        <th >Kasir</th>
                        <th class="text-center" width="5%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sales as $key => $data)
                        @if($data->nominal_in >= $data->pay)
                        @else
                            <tr>
                                <td data-index="{{ $key + 1 }}">{{$key +1}}</td>
                                <td>{{tanggal($data->created_at)}}</td>
                                <td>{{$data->divisi->name}}</td>
                                <td>{{$data->invoice}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->nominal_in)}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('manager.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('manager.sale.invoice')
                                    <button class="btn btn-warning lni lni-dollar btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#examplemodal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Bayar">
                                    </button>
                                    <div class="modal fade" id="examplemodal{{$data->id}}" tabindex="-1"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pembayaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <form action="{{route('manager.sale.bayar', $data->id)}}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger">*</i> Nominal In</label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="hidden" id="nominal_in_value"
                                                                           value="{{ $data->nominal_in }}">
                                                                    <input type="text" class="form-control"
                                                                           id="nominal_in" name="nominal_in"
                                                                           value="{{ formatRupiah($data->nominal_in) }}"
                                                                           readonly>
                                                                    <span
                                                                        class="position-absolute top-50 translate-middle-y"><i
                                                                            class='bx bx-dollar'></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger">*</i> Pay Debts</label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="text" class="form-control"
                                                                           name="pay_debts" id="pay_debts"
                                                                           onkeyup="formatRupiah2(this)">
                                                                    <span
                                                                        class="position-absolute top-50 translate-middle-y"><i
                                                                            class='bx bx-money'></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger">*</i> Date</label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="text" class="form-control datepicker"
                                                                           name="date_pay" id="input42">
                                                                    <span
                                                                        class="position-absolute top-50 translate-middle-y"><i
                                                                            class='bx bx-calendar'></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3" id="bankField">
                                                            <label for="input42"
                                                                   class="col-sm-3 col-form-label">Bank</label>
                                                            <div class="col-sm-9">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-text"><i
                                                                            class="bx bx-credit-card"></i></div>
                                                                    <select class="form-select" id="single-select-field"
                                                                            name="bank_id"
                                                                            data-placeholder="-- Nama Bank --">
                                                                        <option></option>
                                                                        @foreach($bank as $banks)
                                                                            <option
                                                                                value="{{$banks->id}}">{{$banks->name}}
                                                                                ({{$banks->code}})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger"></i> </label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="checkbox" class="form-check"
                                                                           id="lainya">
                                                                    <span class="position-absolute top-50 translate-middle-y ms-1"> Lainya</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3" id="descriptionField">
                                                            <label for="input42"
                                                                   class="col-sm-3 col-form-label"></label>
                                                            <div class="col-sm-9">
                                                                <textarea id="description" type="text"
                                                                          class="form-control" name="description"
                                                                          placeholder="Isi Lainya pembayaran melalui apa?"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary" id="bayarbutton">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{route('manager.sale.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm lni lni-pencil" data-bs-tool="tooltip"
                                       data-bs-placement="top" title="Edit Transaksi"></a>
                                    <button type="button" class="btn btn-danger btn-sm lni lni-close delete-sale"
                                            data-id="{{ $data->id }}"
                                            data-bs-tool="tooltip"
                                            data-bs-placement="top"
                                            title="Batalkan">
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">List Trasaction</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaction" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th class="text-center" width="5%">Tanggal</th>
                        <th>Divisi</th>
                        <th>INV</th>
                        <th>Customer</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Toatl Hrg</th>
                        <th class="text-center" width="5%">Bayar</th>
                        <th width="5%">Kasir</th>
                        <th class="text-center" width="5%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sales as $key => $data)
                        @if($data->nominal_in >= $data->pay)
                            <tr>
                                <td data-index="{{ $key + 1 }}">{{$key +1}}</td>
                                <td>{{tanggal($data->created_at)}}</td>
                                <td>{{$data->divisi->name}}</td>
                                <td>{{$data->invoice}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('manager.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('manager.sale.invoice')
                                    <a href="{{route('manager.sale.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm lni lni-pencil" data-bs-tool="tooltip"
                                       data-bs-placement="top" title="Edit Transaksi"></a>
                                    <button type="button" class="btn btn-danger btn-sm lni lni-close delete-sale"
                                            data-id="{{ $data->id }}"
                                            data-bs-tool="tooltip"
                                            data-bs-placement="top"
                                            title="Batalkan">
                                    </button>
                                </td>

                            </tr>
                        @else
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <style>
        table.dataTable {
            font-size: 12.85px /* Atur ukuran font */
        }


    </style>

@endpush
@push('js')
    <script>
        $(document).ready(function () {
            var table = $('#excel').DataTable({
                lengthChange: false,
                buttons: ['excel'],

            });

            table.buttons().container()
                .appendTo('#excel_wrapper .col-md-6:eq(0)');
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#transaction').DataTable();
        });
    </script>
    <script>
        $(document).ready(function () {
            var table = $('#transaction').DataTable();

            // Mengurutkan ulang nomor saat tabel diurutkan atau difilter
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        });
    </script>
    <script>
        $(document).ready(function () {
            // Inisialisasi Select2 setelah modal dibuka
            $(document).on('shown.bs.modal', function (e) {
                let modal = $(e.target); // Modal yang sedang ditampilkan
                modal.find('#single-select-field').select2({
                    dropdownParent: modal, // Tetapkan parent dropdown ke modal yang aktif
                    placeholder: '-- Nama Bank --',
                    allowClear: true,
                    theme: 'bootstrap-5'
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Fungsi untuk menghitung total nominal_in
            function calculateTotal() {
                // Ambil nilai nominal_in yang sudah dalam angka asli
                let nominal_in = parseFloat($('#nominal_in_value').val()) || 0;

                // Ambil nilai pay_debts dan pastikan nilai default jika kosong atau 0
                let pay_debts = parseFloat($('#pay_debts').val().replace(/[^0-9]/g, '')) || 0;

                // Penjumlahan nominal_in dan pay_debts
                let total = nominal_in + pay_debts;

                // Update nilai nominal_in dengan format Rupiah yang benar
                $('#nominal_in').val('Rp. ' + total.toLocaleString('id-ID'));
            }

            // Event listener untuk input pay_debts
            $('#pay_debts').on('input', function () {
                calculateTotal();  // Menghitung ulang total ketika pay_debts diubah
            });

            // Inisialisasi nilai nominal_in saat halaman dimuat
            calculateTotal();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#submitBtn').click(function (event) {
                // Nonaktifkan tombol dan ubah teksnya
                $(this).prop('bayarbutton', true).text('Memuat...');
            });
        });

        function formatRupiah2(element) {
            let value = element.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            element.value = rupiah;

            function toggleValidation() {
                if ($('#lainya').is(':checked')) {
                    // Jika checkbox lainya dicentang:
                    $('#description').prop('required', true); // Description wajib diisi
                    $('#bankField').hide(); // Sembunyikan bank field
                    $('#single-select-field').prop('required', false); // Bank tidak wajib
                    $('#bankError').hide(); // Sembunyikan pesan error bank
                } else {
                    // Jika checkbox lainya tidak dicentang:
                    $('#description').prop('required', false); // Description tidak wajib
                    $('#bankField').show(); // Tampilkan bank field
                    $('#single-select-field').prop('required', true); // Bank wajib diisi
                }
            }

            // Event listener untuk checkbox lainya
            $('#lainya').on('change', function () {
                toggleValidation();
            });

            // Inisialisasi validasi saat halaman dimuat
            toggleValidation();
        }
    </script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-sale', function () {
                const saleId = $(this).data('id'); // ID dari data yang akan dihapus

                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: "Transaksi Yang Dihapus Tidak Dapat Dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("manager.sale.destroy", ":id") }}'.replace(':id', saleId), // Gunakan route() dengan mengganti placeholder :id
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}', // Kirim token CSRF
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Hapus elemen baris dari DOM
                                    $(`button[data-id="${saleId}"]`).closest('tr').remove();
                                    Swal.fire('Berhasil!', 'Transaksi Berhasil Dihapus', 'success');
                                } else {
                                    Swal.fire('Failed!', response.message || 'Failed to delete sale.', 'error');
                                }
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred while processing your request.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

@endpush


