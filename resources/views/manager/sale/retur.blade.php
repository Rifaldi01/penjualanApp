@extends('layouts.master')

@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Retur Penjualan</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableRetur">
                        <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Invoice Retur</th>
                            <th>Invoice Sales</th>
                            <th>Customer</th>
                            <th>Divisi</th>
                            <th>User</th>
                            <th>Tanggal Retur</th>
                            <th width="12%">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($returSales as $retur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                <span class="badge bg-danger">
                                    {{ $retur->invoice_retur }}
                                </span>
                                </td>
                                <td>{{ $retur->sale->invoice ?? '-' }}</td>
                                <td>{{ $retur->sale->customer->name ?? '-' }}</td>
                                <td>{{ $retur->divisi->name ?? '-' }}</td>
                                <td>{{ $retur->user->name ?? '-' }}</td>
                                <td>{{ dateId($retur->created_at) }}</td>
                                <td>
                                    <a href="{{ route('retur-sales.show', $retur->id) }}"
                                       class="btn btn-sm btn-info">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @if($returSales->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">
                                    Data retur belum tersedia
                                </td>
                            </tr>
                        @endif
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
        $(function () {
            $('#tableRetur').DataTable({
                ordering: true,
                pageLength: 10
            });
        });
    </script>
@endpush

