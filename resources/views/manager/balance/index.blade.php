@extends('layouts.master')
@section('title', 'AKSESORIS BALANCE')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            <form method="GET">
                <div class="row mb-3">

                    <div class="col-md-3">
                        <label>Divisi</label>
                        <select name="divisi_id" class="form-control">
                            <option value="">Semua Divisi</option>

                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}"
                                    {{ $divisiId == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Tanggal Awal</label>
                        <input type="date"
                               name="start_date"
                               class="form-control"
                               min="2026-04-14"
                               value="{{ $startDate }}">
                    </div>

                    <div class="col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date"
                               name="end_date"
                               class="form-control"
                               value="{{ $endDate }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2">
                            Filter
                        </button>

                        <a href="{{ route('manager.balance.index') }}"
                           class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            <div class="table-responsive">

                <table id="tabelBalance" class="table table-bordered table-striped w-100">

                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barcode</th>
                        <th>Aksesoris</th>
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
