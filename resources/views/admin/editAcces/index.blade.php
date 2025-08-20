@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Accessories</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Add Barcode"
                       href="{{ route('manager.acces.create') }}"
                       class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow">
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="box-header with-border">
                <!-- Form untuk cetak barcode -->
                    <div class="table-responsive">
                        <table id="exampleI" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th width="5%">
                                   No
                                </th>
                                <th width="2%">Code </th>
                                <th>Name</th>
                                <th>Divisi</th>
                                <th>Price Cost</th>
                                <th>Sale Price</th>
                                <th class="text-center" width="10%">Stok</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($acces as $key => $data)
                                <tr>
                                    <td>
                                        {{$key +1}}
                                    </td>
                                    <td>{{ $data->code_acces }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->divisi->name }}</td>
                                    <td>{{ is_numeric($data->capital_price) ? formatRupiah($data->capital_price, 0, '.', '.') : '0' }},-</td>
                                    <td>{{ formatRupiah($data->price) }},-</td>
                                    <td class="text-center">{{ $data->stok }}</td>
                                    <td class="text-center">
                                        <a href="{{route('admin.acces.edit', $data->id)}}"
                                           class="btn btn-sm btn-dnd bx bx-edit me-1"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        @endsection

        @push('js')
            <script>
                // Checkbox untuk memilih semua accessories

                $(document).ready(function () {
                    $('#exampleI').DataTable({
                        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
                        pageLength: 10, // Default halaman pertama
                        // Untuk tampilan responsif
                    });
                });
            </script>
    @endpush

