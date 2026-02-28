@extends('layouts.master')

@section('content')

    <div class="container">

        <h4>
            Detail Balance Tahun {{ $balance->year }}
            - {{ $balance->divisi->nama }}
        </h4>

        <div class="card mb-4">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-3">
                        <strong>Capital Stock</strong><br>
                        {{ number_format($balance->capital_stock) }}
                    </div>

                    <div class="col-md-3">
                        <strong>Total In</strong><br>
                        {{ number_format($balance->accessories_in) }}
                    </div>

                    <div class="col-md-3">
                        <strong>Total Sale</strong><br>
                        {{ number_format($balance->sale) }}
                    </div>

                    <div class="col-md-3">
                        <strong>Remainder</strong><br>
                        {{ number_format($balance->remainder) }}
                    </div>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped" id="example">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Accessories</th>
                        <th>Stok Awal</th>
                        <th>Barang Masuk</th>
                        <th>Barang Terjual</th>
                        <th>Barang Dikembalikan</th>
                        <th>Barang Rusak</th>
                        <th>Barang Diminta</th>
                        <th>Minta Barang</th>
                        <th>Sisa</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($balance->details as $key => $detail)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $detail->accessory->code_acces ?? 'kosong' }}</td>
                            <td>{{ $detail->accessory->name ?? 'kososng' }}</td>
                            <td>{{ number_format($detail->accessories_capital_stock) }}</td>
                            <td>{{ number_format($detail->accessories_in) }}</td>
                            <td>{{ number_format($detail->accessories_sale) }}</td>
                            <td>{{ number_format($detail->accessories_retur) }}</td>
                            <td>{{ number_format($detail->accessories_reject) }}</td>
                            <td>{{ number_format($detail->accessories_requested) }}</td>
                            <td>{{ number_format($detail->accessories_requested_in) }}</td>
                            <td>
                                <strong>
                                    {{ number_format($detail->accessories_balance) }}
                                </strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                Tidak ada data detail
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>

@endsection
