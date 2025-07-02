@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Nama Divisi</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="lni lni-apartment"></i></a></li>
                    <li id="breadcrumbDivisiName" class="breadcrumb-item active" aria-current="page">{{ $userDivisi }}</li>
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
                    <div class="col-sm-6">
                        <h4>Pembelian Barang</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-end me-2">
                            <a href="{{route('manager.pembelian.create')}}"
                               class="btn btn-dnd btn-sm mb-3 bx bx-plus"></a>
                        </div>
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
                            <th>Divisi</th>
                            <th >Total Barang</th>
                            <th >Total Harga</th>
                            <th >Status</th>
                            <th >Actions</th>
                        </tr>
                        </thead>
                        <tbody id="pembelian">
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
                                <td>
                                    @if($data->divisi_id)
                                        {{$data->divisi->name}}
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
                                    <a href="{{route('manager.pembelian.edit', $data->id)}}"
                                       class="btn btn-warning btn-sm bx bx-edit" data-bs-toggle="tooltip"
                                       data-bs-placement="top" title="Edit Data"></a>
                                    <a href="{{ route('manager.pembelian.destroy', $data->id) }}" data-confirm-delete="true"
                                       class="btn btn-danger btn-sm bx bx-trash" data-bs-toggle="tooltip"
                                       data-bs-placement="top" title="Delete">
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">Total Hutang</th>
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
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        $(document).ready(function () {
            var table = $('#pembelian').DataTable({
                lengthChange: false,
                buttons: ['excel', 'pdf', 'print']
            });

            $('#divisiFilter').on('change', function () {
                const divisiId = this.value;
                const breadcrumbDivisiName = document.getElementById('breadcrumbDivisiName');
                const selectedOption = this.options[this.selectedIndex].text;

                // Update nama divisi di breadcrumb
                if (divisiId) {
                    breadcrumbDivisiName.textContent = selectedOption;
                }

                // Fetch data pembelian berdasarkan divisi
                fetch(`/manager/pembelian/filter/${divisiId}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#pembelian tbody');
                        tbody.innerHTML = ''; // Kosongkan tabel sebelum menambahkan data baru

                        if (data.length > 0) {
                            data.forEach((item, index) => {
                                const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.invoice}</td>
                                    <td>${item.supplier ? item.supplier.name : 'Tidak ada Supplier'}</td>
                                    <td>${item.divisi ? item.divisi.name : 'Tidak ada Divisi'}</td>
                                    <td>${item.total_item || '0'}</td>
                                    <td>${formatRupiah(item.total_harga)}</td>
                                    <td>
                                        ${item.status == 0
                                    ? '<span class="badge bg-success">Lunas</span>'
                                    : '<span class="badge bg-danger">Belum Lunas</span>'}
                                    </td>
                                    <td>
                                        <a href="{{route('manager.pembelian.edit', $data->id)}}"
                                           class="btn btn-warning btn-sm bx bx-edit" data-bs-toggle="tooltip"
                                           data-bs-placement="top" title="Edit Data"></a>
                                        <form action="{{route('manager.pembelian.destroy', $data->id)}}" method="POST" style="display:inline-block;">
                                            @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm bx bx-trash"
                                        data-confirm-delete="true" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Hapus Data"></button>
                            </form>
                        </td>
                    </tr>
`;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML = `
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data untuk divisi yang dipilih</td>
                            </tr>
                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data pembelian:', error);
                        const tbody = document.querySelector('#pembelian tbody');
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center">Terjadi kesalahan dalam mengambil data</td>
                        </tr>
                    `;
                    });
            });

            table.buttons().container()
                .appendTo('#pembelian_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
