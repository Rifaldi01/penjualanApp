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
                <table id="excel" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th class="text-center" width="5%">Tanggal</th>
                        <th class="text-center" width="5%">Invoice</th>
                        <th>Customer</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Uang Msk</th>
                        <th class="text-center" width="5%">Total Price</th>
                        <th class="text-center" width="5%">Ppn</th>
                        <th class="text-center" width="5%">Pph</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Total Pay</th>
                        <th width="5%">Kasir</th>
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
                                <td>{{$data->invoice}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->nominal_in)}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->ppn)}}</td>
                                <td>{{formatRupiah($data->pph)}}</td>
                                <td>{{formatRupiah($data->diskon)}}</td>
                                <td>{{formatRupiah($data->ongkir)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('admin.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('admin.sale.invoice')
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
                                                <form action="{{route('admin.sale.update', $data->id)}}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger">*</i> Uang Masuk</label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="hidden" id="nominal_in_value_{{$data->id}}"
                                                                           value="{{ $data->nominal_in }}">
                                                                    <input type="text" class="form-control"
                                                                           id="nominal_in_{{$data->id}}" name="nominal_in"
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
                                                                           name="pay_debts" id="pay_debts_{{$data->id}}"
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
                                                        <div class="row mb-3" id="bankField_{{$data->id}}">
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
                                                        <div class="row mb-3" id="penerimaField_{{$data->id}}">
                                                            <label for="input42" class="col-sm-3 col-form-label">Penerima</label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="text" class="form-control"
                                                                           name="penerima" id="penerima_{{$data->id}}">
                                                                    <span class="position-absolute top-50 translate-middle-y"><i
                                                                            class='bx bx-user'></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label for="input42" class="col-sm-3 col-form-label"><i
                                                                    class="text-danger"></i> </label>
                                                            <div class="col-sm-9">
                                                                <div class="position-relative input-icon">
                                                                    <input type="checkbox" class="form-check"
                                                                           id="lainya_{{$data->id}}">
                                                                    <span class="position-absolute top-50 translate-middle-y ms-1"> Lainya</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3" id="descriptionField">
                                                            <label for="input42"
                                                                   class="col-sm-3 col-form-label"></label>
                                                            <div class="col-sm-9">
                                                                <textarea id="description_{{$data->id}}" type="text"
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
                        <th class="text-center" width="5%">Invoice</th>
                        <th>Customer</th>
                        <th class="text-center" width="5%">Total Item</th>
                        <th class="text-center" width="5%">Total Price</th>
                        <th class="text-center" width="5%">Ppn</th>
                        <th class="text-center" width="5%">Pph</th>
                        <th class="text-center" width="5%">Diskon</th>
                        <th class="text-center" width="5%">Ongkir</th>
                        <th class="text-center" width="5%">Total Pay</th>
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
                                <td>{{$data->invoice}}</td>
                                <td>{{$data->customer->name}}</td>
                                <td class="text-center">{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->total_price)}}</td>
                                <td>{{formatRupiah($data->ppn)}}</td>
                                <td>{{formatRupiah($data->pph)}}</td>
                                <td>{{formatRupiah($data->diskon)}}</td>
                                <td>{{formatRupiah($data->ongkir)}}</td>
                                <td>{{formatRupiah($data->pay)}}</td>
                                <td>{{$data->user->name}}</td>
                                <td>
                                    <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleExtraLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Surat Jalan">
                                    </button>
                                    @include('admin.sale.surat-jalan')
                                    <button type="button" class="btn btn-primary lni lni-empty-file btn-sm"
                                            data-bs-toggle="modal" id="btn-print{{$data->id}}"
                                            data-bs-target="#exampleLargeModal{{$data->id}}" data-bs-tool="tooltip"
                                            data-bs-placement="top" title="Print Invoice">
                                    </button>
                                    @include('admin.sale.invoice')
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
            font-size: 11px /* Atur ukuran font */
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@push('js')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-sale', function () {
                const saleId = $(this).data('id'); // ID dari data yang akan dihapus

                Swal.fire({
                    title: 'Batalkan Transaksi?',
                    text: "Transaksi Yang Dibatakan Tidak Akan Dapat Dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.sale.destroy", ":id") }}'.replace(':id', saleId), // Gunakan route() dengan mengganti placeholder :id
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}', // Kirim token CSRF
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Hapus elemen baris dari DOM
                                    $(`button[data-id="${saleId}"]`).closest('tr').remove();
                                    Swal.fire('Berhasil!', 'Transaksi Berhasil Dibatalkan', 'success');
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

    <script>
        $(document).ready(function () {
            var table = $('#excel').DataTable({
                lengthChange: false,
                buttons: ['excel']
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
            var table = $('#example').DataTable();

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
            function calculateTotal(id) {
                let nominal_in = parseFloat($(`#nominal_in_value_${id}`).val()) || 0;
                let pay_debts = parseFloat($(`#pay_debts_${id}`).val().replace(/[^0-9]/g, '')) || 0;

                let total = nominal_in + pay_debts;
                $(`#nominal_in_${id}`).val('Rp. ' + total.toLocaleString('id-ID'));
            }

            $('[id^=pay_debts_]').on('input', function () {
                let id = $(this).attr('id').split('_')[2];
                calculateTotal(id);
            });

            $('[id^=nominal_in_value_]').each(function () {
                let id = $(this).attr('id').split('_')[2];
                calculateTotal(id);
            });
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

            function toggleValidation(id) {
                if ($(`#lainya_${id}`).is(':checked')) {
                    // Jika checkbox lainya dicentang:
                    $(`#description_${id}`).prop('required', true); // Description wajib diisi
                    $(`#bankField_${id}`).hide(); // Sembunyikan bank field
                    $(`#single-select-field_${id}`).prop('required', false); // Bank tidak wajib
                } else {
                    // Jika checkbox lainya tidak dicentang:
                    $(`#description_${id}`).prop('required', false); // Description tidak wajib
                    $(`#bankField_${id}`).show(); // Tampilkan bank field
                    $(`#single-select-field_${id}`).prop('required', true); // Bank wajib diisi
                }
            }

            // Event listener untuk checkbox lainya
            $("[id^='lainya_']").on('change', function () {
                var id = $(this).attr('id').split('_')[1]; // Ambil ID dinamis
                toggleValidation(id);
            });

            // Inisialisasi validasi saat halaman dimuat
            $("[id^='lainya_']").each(function () {
                var id = $(this).attr('id').split('_')[1]; // Ambil ID dinamis
                toggleValidation(id);
            });
        }
    </script>

@endpush

