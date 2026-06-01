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
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <form method="GET">
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <select name="year" class="form-control">

                                @for($y = 2026; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}"
                                        {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor

                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="month" class="form-control">

                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}"
                                        {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endfor

                            </select>
                        </div>

                        <div class="col-md-3">
                            <button class="btn btn-primary">
                                Filter
                            </button>
                        </div>

                    </div>
                </form>
                <table id="tabelBalance" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Accessories</th>
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

