@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List reject accessories</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Add Barcode"
                       href="{{ route('gudang.acces.kembali') }}"
                       class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow">
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="box-header with-border">
                <div class="table-responsive">
                    <table id="exampleA" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th width="5%">
                                No
                            </th>
                            <th width="2%">Code Accessories</th>
                            <th>Nama Accessories</th>
                            <th>Price</th>
                            <th class="text-center" width="10%">Stok</th>
                            <th class="text-center" width="10%">Keterangan</th>
                            <th class="text-center" width="10%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reject as $key => $data)
                            <tr>
                                <td>
                                    {{$key +1}}
                                </td>
                                <td>{{ $data->code_acces }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ formatRupiah($data->price) }},-</td>
                                <td class="text-center">{{ $data->stok }}</td>
                                <td class="text-center">{{$data->keterangan}}</td>
                                <td class="text-center">
                                    <a href="#"
                                       class="btn btn-danger btn-sm delete-accessory bx bx-trash"
                                       data-id="{{ $data->id }}"
                                       data-url="{{ route('gudang.acces.deletReject', $data->id) }}"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Hapus">
                                    </a>

{{--                                    <a href="" class="btn btn-warning bx bx-reset btn-sm"data-bs-toggle="tooltip"--}}
{{--                                       data-bs-placement="top" title="Diperbaiki" ></a>--}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
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
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-accessory');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.dataset.url;

                    Swal.fire({
                        title: 'Apakah kamu yakin?',
                        text: "Data ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#exampleA').DataTable({
                lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
                pageLength: 10, // Default halaman pertama
                responsive: true, // Untuk tampilan responsif
                dom: 'Bfrtip', // Menambahkan area untuk tombol di atas tabel
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Accessories Reject',// Tombol untuk Excel
                        text: 'Excel',
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Accessories Reject',// Tombol untuk Excel// Tombol untuk PDF
                        text: 'PDF',
                    }
                ]
            });
        });
    </script>
@endpush

