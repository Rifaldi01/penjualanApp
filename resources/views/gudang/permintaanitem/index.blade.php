@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="container">
            <div class="card-header mt-3 mb-3">
                <div class="row">
                    <div class="col-sm">
                        <h4>DAFTAR MEMINTA</h4>
                    </div>
                    <div class="col-sm">
                        <div class="float-end">
                            <a href="{{route('gudang.permintaanitem.create')}}" class="btn btn-dnd bx bx-file"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Meminta Accessories"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="example2">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>No Seri</th>
                        <th>Jumlah</th>
                        <th>Asal Divisi</th>
                        <th width="2%">Status</th>
                        <th width="2%">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permintaans as $key => $permintaan)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{ tanggal($permintaan->created_at) }}</td>
                            <td>{{ $permintaan->kode }}</td>
                            <td>
                                @foreach($permintaan->detailItem as $item)
                                    <li>{{ $item->itemIn ? $item->itemIn->name : '-' }}</li>
                                @endforeach
                            </td>
                            <td>
                                @foreach($permintaan->detailItem as $item)
                                    <li>{{ $item->itemIn ? $item->itemIn->no_seri : '-' }}</li>
                                @endforeach
                            </td>

                            <td>{{ $permintaan->jumlah }}</td>
                            <td>{{ $permintaan->divisiAsal->name }}</td>
                            <td>
                                @if($permintaan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($permintaan->status == 'disetujui')
                                    <span class="badge bg-success ">Disetujui</span>
                                @else
                                    <span class="badge bg-primary ">Diterima</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_tujuan
                                    && in_array($permintaan->status, ['pending', 'disetujui']))
                                    <!-- Tombol Setujui -->
                                    <form id="delete-form-{{ $permintaan->id }}"
                                          action="{{ route('gudang.permintaanitem.destroy', $permintaan->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0)"
                                       data-id="{{ $permintaan->id }}"
                                       class="btn btn-danger btn-sm btn-delete bx bx-trash"
                                       data-bs-toggle="tooltip"
                                       title="Batal">
                                    </a>
                                @endif
                                @if(Auth::user()->divisi_id == $permintaan->divisi_id_tujuan && $permintaan->status == 'disetujui')
                                    <!-- Tombol Setujui -->
                                    <form action="{{ route('gudang.permintaanitem.approve', $permintaan->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm bx bx-check" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Diterima">
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
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                let id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Yakin ingin Membatalkan?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush

