@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Nama Divisi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="lni lni-apartment"></i></a></li>
                    <li id="breadcrumbDivisiName" class="breadcrumb-item active" aria-current="page">{{$divisiUser}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <select id="divisiFilter" class="form-select select2">
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisi as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="container mt-3">
            <div class="card-head">
                <div class="row">
                    <div class="col-sm-6"> <h4>Supplier Management</h4></div>
                    <div class="col-sm-6">
                        <div class="float-end me-2">
                            <button class="btn btn-dnd btn-sm mb-3 bx bx-plus" id="createNewSupplier"></button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Name</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($suppliers as $key => $supplier)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $supplier->kode }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->alamat }}</td>
                        <td>{{ $supplier->telepon }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editSupplier" data-id="{{ $supplier->id }}">Edit</button>
                            <button class="btn btn-danger btn-sm deleteSupplier" data-id="{{ $supplier->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#divisiFilter').change(function() {
                var selectedDivisi = $('#divisiFilter option:selected').text();
                $('#breadcrumbDivisiName').text(selectedDivisi);
                window.location.href = "?divisi_id=" + $(this).val();
            });
        });
    </script>
@endpush
