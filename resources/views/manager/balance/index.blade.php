@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">
                            Accessories Balance
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            <form method="GET">
                <div class="row mb-3">

                    <div class="col-md-3">
                        <select name="divisi_id" class="form-control">
                            <option value="">
                                Semua Divisi
                            </option>

                            @foreach($divisis as $itemDivisi)
                                <option value="{{ $itemDivisi->id }}"
                                    {{ $divisiId == $itemDivisi->id ? 'selected' : '' }}>
                                    {{ $itemDivisi->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="month" class="form-control">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                    {{ $month == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0,0,0,$i,1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="year" class="form-control">
                            @for($i = 2026; $i <= date('Y') + 5; $i++)
                                <option value="{{ $i }}"
                                    {{ $year == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>
                    </div>

                </div>
            </form>

            <div class="table-responsive">

                <table id="tabelBalance" class="table table-bordered table-striped w-100">

                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Accessories</th>
                        <th>Saldo Awal</th>
                        <th>Masuk</th>
                        <th>Retur</th>
                        <th>Req Masuk</th>
                        <th>Terjual</th>
                        <th>Rusak</th>
                        <th>Req Keluar</th>
                        <th>Saldo Akhir</th>
                    </tr>
                    </thead>

                    <tbody>

                    @forelse($data as $key => $row)

                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ number_format($row->saldo_awal) }}</td>
                            <td>{{ number_format($row->barang_masuk) }}</td>
                            <td>{{ number_format($row->barang_retur) }}</td>
                            <td>{{ number_format($row->permintaan_masuk) }}</td>
                            <td>{{ number_format($row->barang_terjual) }}</td>
                            <td>{{ number_format($row->barang_rusak) }}</td>
                            <td>{{ number_format($row->permintaan_keluar) }}</td>

                            <td>
                                <strong>
                                    {{ number_format($row->saldo_akhir) }}
                                </strong>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="12" class="text-center">
                                Tidak ada data
                            </td>
                        </tr>

                    @endforelse
                    </tbody>

                </table>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $('#tabelBalance').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                pageLength: 25,
                lengthChange: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        footer: true
                    },
                    {
                        extend: 'pdf',
                        footer: true
                    },
                    {
                        extend: 'print',
                        footer: true
                    }
                ]
            });

        });
    </script>
@endpush
