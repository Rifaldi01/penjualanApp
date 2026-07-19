@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">Daftar Divisi</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a href="{{route('superadmin.divisi.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"
                    ><i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="2%">Logo</th>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Invoice Format</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="9%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($divisi as $key => $data)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>
                                <img src="{{asset('images/logo/'. $data->logo)}}" alt="{{$data->name}}" width="100%">
                            </td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->kode}}</td>
                            <td>{{$data->inv_format}}</td>
                            <th>
                                @if($data->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Non-Active</span>
                                @endif
                            </th>
                            <td>
                                <a href="{{route('superadmin.divisi.edit', $data->id)}}" class="btn btn-sm btn-warning bx bx-edit" data-bs-tool="tooltip"
                                   data-bs-placement="top" title="Edit"></a>
                                @if($data->status == 'active')
                                    <form action="{{ route('superadmin.divisi.block', $data->id) }}" method="POST" class="d-inline form-block">
                                        @csrf
                                        @method('PUT')

                                        <button type="submit" class="btn btn-sm btn-danger bx bx-block" data-bs-tool="tooltip"
                                                data-bs-placement="top" title="Non-Aktif">
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('superadmin.divisi.activ', $data->id) }}" method="POST" class="d-inline form-active">
                                        @csrf
                                        @method('PUT')

                                        <button type="submit" class="btn btn-sm btn-success bx bx-check" data-bs-tool="tooltip"
                                                data-bs-placement="top" title="Aktif">
                                        </button>
                                    </form>
                                @endif
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
        document.addEventListener('DOMContentLoaded', function () {

            // Non Aktif
            document.querySelectorAll('.form-block').forEach(function(form){
                form.addEventListener('submit', function(e){
                    e.preventDefault();

                    Swal.fire({
                        title: 'Nonaktifkan Divisi?',
                        text: "Divisi tidak dapat digunakan sampai diaktifkan kembali.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Nonaktifkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if(result.isConfirmed){
                            form.submit();
                        }
                    });
                });
            });

            // Aktifkan
            document.querySelectorAll('.form-active').forEach(function(form){
                form.addEventListener('submit', function(e){
                    e.preventDefault();

                    Swal.fire({
                        title: 'Aktifkan Divisi?',
                        text: "Divisi akan dapat digunakan kembali.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Aktifkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if(result.isConfirmed){
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>
@endpush
