@extends('layouts.master')
@section('title', 'AKSESORIS BALANCE')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <form method="GET">
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label>Tanggal Awal</label>
                            <input type="date"
                                   name="start_date"
                                   class="form-control"
                                   value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date"
                                   name="end_date"
                                   class="form-control "
                                   value="{{ request('end_date') }}">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                Filter
                            </button>

                            <a href="{{ url()->current() }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
                <table id="tabelBalance" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Kode Barcode</th>
                        <th>Aksesoris</th>
                        <th>Saldo Awal</th>
                        <th>Masuk</th>
                        <th>Retur</th>
                        <th>Req {{Auth::user()->divisi->name}}</th>
                        <th>Terjual</th>
                        <th>Rusak</th>
                        <th>Req Non {{Auth::user()->divisi->name}}</th>
                        <th>Saldo Akhir</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($data as $row)

                        <tr>

                            <td>{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->saldo_awal }}</td>
                            <td>{{ $row->barang_masuk }}</td>
                            <td>{{ $row->barang_retur }}</td>
                            <td>{{ $row->permintaan_masuk }}</td>
                            <td>{{ $row->barang_terjual }}</td>
                            <td>{{ $row->barang_rusak }}</td>
                            <td>{{ $row->permintaan_keluar }}</td>

                            <td>
                                <strong>{{ $row->saldo_akhir }}</strong>
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
        $(document).ready(function() {
            var table = $('#tabelBalance').DataTable( {
                order: [[1, 'asc']],
                lengthChange: false,
                buttons: ['excel', 'pdf', 'print']
            } );

            table.buttons().container()
                .appendTo( '#tabelBalance_wrapper .col-md-6:eq(0)' );
        } );
    </script>
@endpush

