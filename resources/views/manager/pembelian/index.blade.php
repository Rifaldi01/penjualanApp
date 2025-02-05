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
                            <th rowspan="2" style="border-top: 2px">No</th>
                            <th rowspan="2">Invoice</th>
                            <th rowspan="2">Supplier</th>
                            <th colspan="3" class="text-center">Barang</th>
                            <th rowspan="2">Total Barang</th>
                            <th rowspan="2">Total Harga</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Actions</th>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Qty</th>
                        </tr>
                        </thead>
                        <tbody id="pembelian">
                        @foreach($pembelian as $key => $data)
                            <tr>
                                <td>{{$key +1}}</td>
                                <td>{{$data->invoice}}</td>
                                <td>{{$data->supplier->name}}</td>
                                <td>
                                    @foreach($data->ItemBeli as $item)
                                        <li>{{ $item->name }}</li>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data->ItemBeli as $item)
                                        <li>{{ formatRupiah($item->harga) }}</li>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($data->ItemBeli as $item)
                                        <li>{{ $item->qty }}</li>
                                    @endforeach
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
                                    <form action="{{ route('manager.pembelian.destroy', $data->id) }}" method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm bx bx-trash"
                                                data-confirm-delete="true" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="hapus Data"></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="8" class="text-end">Total Hutang</th>
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
                fetch(`/penjualanApp/public/manager/pembelian/filter/${divisiId}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#pembelian tbody');
                        tbody.innerHTML = ''; // Kosongkan tabel sebelum menambahkan data baru

                        if (data.length > 0) {
                            data.forEach((item, index) => {
                                const itemRows = item.item_beli.map(i => `
                                <li>${i.name}</li>
                            `).join('');

                                const hargaRows = item.item_beli.map(i => `
                                <li>${formatRupiah(i.harga)}</li>
                            `).join('');

                                const qtyRows = item.item_beli.map(i => `
                                <li>${i.qty}</li>
                            `).join('');

                                const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.invoice}</td>
                                    <td>${item.supplier ? item.supplier.name : 'Tidak ada Supplier'}</td>
                                    <td>${itemRows || 'Tidak ada Barang'}</td>
                                    <td>${hargaRows || 'Tidak ada Harga'}</td>
                                    <td>${qtyRows || 'Tidak ada Qty'}</td>
                                    <td>${item.total_item}</td>
                                    <td>${formatRupiah(item.total_harga)}</td>
                                    <td>
                                        ${item.status == 0
                                    ? '<span class="badge bg-success">Lunas</span>'
                                    : '<span class="badge bg-danger">Belum Lunas</span>'}
                                    </td>
                                    <td>
                                        <a href="/penjualanApp/public/manager/pembelian/edit/${item.id}"
                                           class="btn btn-warning btn-sm bx bx-edit" data-bs-toggle="tooltip"
                                           data-bs-placement="top" title="Edit Data"></a>
                                        <form action="/penjualanApp/public/manager/pembelian/destroy/${item.id}" method="POST" style="display:inline-block;">
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
