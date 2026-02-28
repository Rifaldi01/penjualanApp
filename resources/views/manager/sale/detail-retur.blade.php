@extends('layouts.master')
@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Detail Retur</h5>
            <div>
                <a href="{{ route('retur.print', $retur->id) }}" class="btn btn-secondary">Print</a>
                <a href="{{ route('retur.pdf', $retur->id) }}" class="btn btn-danger">PDF</a>
                <a href="{{ route('retur.excel', $retur->id) }}" class="btn btn-success">Excel</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-sm">
                <tr>
                    <th>Invoice Sale</th>
                    <td>{{ $retur->sale->invoice }}</td>
                </tr>
                <tr>
                    <th>Invoice Retur</th>
                    <td>{{ $retur->invoice_retur }}</td>
                </tr>
                <tr>
                    <th>Divisi</th>
                    <td>{{ $retur->divisi->name }}</td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>{{ $retur->user->name }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ dateId($retur->created_at) }}</td>
                </tr>
            </table>

            <hr>

            <h6>Detail Barang</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                <tr>
                    <th>Tipe</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>

                {{-- ITEM --}}
                @foreach ($retur->sale?->itemSales ?? [] as $item)
                    <tr>
                        <td>
                            <span class="badge bg-primary">Item</span>
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->no_seri }}</td>
                        <td class="text-center">1</td>
                        <td class="text-end">{{ number_format($item->price) }}</td>
                        <td class="text-end">{{ number_format($item->price) }}</td>
                    </tr>
                @endforeach

                {{-- ACCESSORIES --}}
                @foreach ($retur->sale?->accessoriesSales ?? [] as $acc)
                    <tr>
                        <td>
                            <span class="badge bg-success">Accessories</span>
                        </td>
                        <td>{{ $acc->accessories?->name ?? 'Aksesoris dihapus' }}</td>
                        <td>{{ $acc->accessories?->code_acces ?? 'Aksesoris dihapus' }}</td>
                        <td class="text-center">{{ $acc->qty }}</td>
                        <td class="text-end">
                            {{ formatRupiah($acc->subtotal / max($acc->qty,1)) }}
                        </td>
                        <td class="text-end">{{ formatRupiah($acc->subtotal) }}</td>
                    </tr>
                @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4" class="text-center bg-light">Total Price</th>
                    <th colspan="2" class="text-center bg-light">{{formatRupiah($retur->sale->total_price)}}</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <div class="mb-2">
                <table width="100%">
                    <tr>
                        <td width="50%" class="text-center">
                            Dibuat Oleh<br><br><br>
                            <br><br><br>
                            {{ $retur->user->name ?? '-' }}
                        </td>
                        <td width="50%" class="text-center">
                            Diterima Oleh<br><br><br>
                            <br><br><br>
                            ___________________
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


@endsection
@push('head') @endpush
@push('js') @endpush
