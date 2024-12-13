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
                <form id="printBarcodeForm" action="{{ route('manager.acces.print') }}" method="POST" target="_blank">
                    @csrf
                    <div class="btn-group mb-1">
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="bx bx-barcode"></i> Print Barcode
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="exampleI" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th width="2%">Code Access</th>
                                <th>Name</th>
                                <th>Modal</th>
                                <th>Price</th>
                                <th class="text-center">Jumlah Barcode</th>
                                <th class="text-center" width="10%">Stok</th>
                                <th class="text-center" width="10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($acces as $data)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="accessories[]" value="{{ $data->id }}" class="select_item" form="printBarcodeForm">
                                    </td>
                                    <td>{{ $data->code_acces }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ formatRupiah($data->capital_price) }},-</td>
                                    <td>{{ formatRupiah($data->price) }},-</td>
                                    <td class="text-center">
                                        <input type="number" name="barcode_quantity[{{ $data->id }}]" placeholder="Jumlah Barcode" class="form-control" min="1">                                    </td>
                                    <td class="text-center">{{ $data->stok }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.acces.destroy', $data->id) }}" data-confirm-delete="true"
                                           class="btn btn-danger btn-sm bx bx-trash" data-bs-toggle="tooltip"
                                           data-bs-placement="top" title="Delete"></a>
                                        <a href="{{ route('manager.acces.edit', $data->id) }}"
                                           class="btn btn-sm btn-dnd bx bx-edit me-1"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
        @endsection

        @push('js')
            <script>
                // Checkbox untuk memilih semua accessories
                document.getElementById('select_all').addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.select_item');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
                $(document).ready(function () {
                    $('#exampleI').DataTable({
                        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
                        pageLength: 10, // Default halaman pertama
                        // Untuk tampilan responsif
                    });
                });
            </script>
    @endpush

