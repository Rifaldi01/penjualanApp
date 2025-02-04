@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Items</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Add Item"
                       href="{{route('manager.item.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"><i
                            class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="box-header with-border">
                <!-- Form untuk cetak barcode -->
                <form id="printBarcodeForm" action="{{ route('manager.item.print') }}" method="POST" target="_blank">
                    @csrf
                    <div class="btn-group mb-1">
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="bx bx-barcode"></i> Print Barcode
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table id="exampleI" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="5%">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th width="2%">No</th>
                        <th>Divisi</th>
                        <th>Name</th>
                        <th>No Seri</th>
                        <th>Price Cost</th>
                        <th>Sale Price</th>
                        <th class="text-center" width="5%">Jumalah Barcode</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $key => $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="items[]" value="{{ $item->id }}" class="select_item" form="printBarcodeForm">
                            </td>
                            <td>{{$key + 1}}</td>
                            <td>
                                {{ $item->divisi->name }}
                            </td>
                            <td>
                                <a class="text-dark">{{$item->name}}</a>
                            </td>
                            <td>{{$item->cat->name}}-{{$item->no_seri}}</td>
                            <td>{{formatRupiah($item->capital_price)}},-</td>
                            <td>{{formatRupiah($item->price)}},-</td>
                            <td class="text-center">
                                <input type="number" class="form-control" value="1" readonly>
                            </td>
                            <td class="text-center">
                                <a href="{{route('manager.item.destroy', $item->id)}}" data-confirm-delete="true"
                                   class="btn btn-danger btn-sm bx bx-trash" data-bs-toggle="tooltip"
                                   data-bs-placement="top" title="Delete">
                                </a>
                                <a href="{{route('manager.item.edit', $item->id)}}"
                                   class="btn btn-sm btn-dnd bx bx-edit me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')

@endpush
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
