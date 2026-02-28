@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">Accessories Balance</h4>
                    </div>
                </div>
                <div class="col-6 mt-3">
                    <div class="btn-group float-end me-3">
                        <select id="divisiFilter" class="form-select select2">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisi as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="balance" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th>Tahun</th>
                        <th>Divisi</th>
                        <th>Stok Awal</th>
                        <th>Barang Masuk</th>
                        <th>Barang Terjual</th>
                        <th>Barang Dikembalikan</th>
                        <th>Barang Rusak</th>
                        <th>Barang Diminta</th>
                        <th>Minta Barang</th>
                        <th>Sisa</th>
                        <th width="12%" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="dataBalace">
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

            let table = $('#balance').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('accessories.balance.data') }}",
                    data: function (d) {
                        d.divisi_id = $('#divisiFilter').val();
                    }
                },
                columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    { data: 'year', render: data => 'Tahun ' + data },
                    { data: 'divisi.name', defaultContent: '-' },
                    { data: 'capital_stock' },
                    { data: 'accessories_in' },
                    { data: 'sale' },
                    { data: 'retur' },
                    { data: 'reject' },
                    { data: 'request' },
                    { data: 'request_in' },
                    { data: 'remainder' },
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function (data, type, row) {

                            let detailUrl = "{{ route('manager.balance.show', ':id') }}";
                            detailUrl = detailUrl.replace(':id', row.id);



                            return `
                            <a href="${detailUrl}"
                               class="btn btn-info lni lni-eye"
                               title="Detail"></a>

                            <a href="/"
                               class="btn btn-warning lni lni-pencil"
                               title="Edit"></a>
                        `;
                        }

                    }
                ]
            });

            $('#divisiFilter').on('change', function () {
                table.ajax.reload();
            });

            $('.select2').select2();
        });
    </script>
@endpush

