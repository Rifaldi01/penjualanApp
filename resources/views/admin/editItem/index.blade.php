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
            </div>
        </div>
        <div class="card-body">
            <div class="box-header with-border">
                <!-- Form untuk cetak barcode -->
            </div>

            <div class="table-responsive">
                <table id="exampleI" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Divisi</th>
                        <th>Name</th>
                        <th>No Seri</th>
                        <th>Price Cost</th>
                        <th>Sale Price</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $key => $item)
                        <tr>
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
                                <a href="{{route('admin.item.edit', $item->id)}}"
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
        $(document).ready(function () {
            $('#exampleI').DataTable({
                lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
                pageLength: 10, // Default halaman pertama
                // Untuk tampilan responsif
            });
        });
    </script>
@endpush
