@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="container mt-3">
            <div class="card-head">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Pembelian Barang</h4>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pembelian" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th  style="border-top: 2px">No</th>
                            <th >Invoice</th>
                            <th >Supplier</th>
                            <th >Total Barang</th>
                            <th >Total Harga</th>
                            <th >Status</th>
                            <th >Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pembelian as $key => $data)
                            <tr>
                                <td>{{$key +1}}</td>
                                <td>{{$data->invoice}}</td>
                                <td>
                                    @if($data->supplier_id)
                                        {{$data->supplier->name}}
                                    @else
                                        <div class="text-danger">Lengkapi pembelian</div>
                                    @endif
                                </td>

                                <td>{{$data->total_item}}</td>
                                <td>{{formatRupiah($data->total_harga)}}</td>
                                <td>
                                    @if($data->status == 0)
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('admin.pembelian.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm bx bx-edit" data-bs-toggle="tooltip"
                                       data-bs-placement="top" title="Edit Data"></a>
                                    <form action="{{ route('admin.pembelian.destroy', $data->id) }}" method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm bx bx-trash"
                                                data-confirm-delete="true" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="hapus Data">

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total Hutang</th>
                            <td colspan="2" class="text-center">{{ formatRupiah($totalHarga) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('head')

@endpush
@push('js')
    <script>
        $(document).ready(function () {
            var table = $('#pembelian').DataTable({
                lengthChange: false,
                buttons: ['excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#pembelian_wrapper .col-md-6:eq(0)');
        });
    </script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endpush
