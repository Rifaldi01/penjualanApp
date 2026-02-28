<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Retur</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<h3 class="text-center">NOTA RETUR PENJUALAN</h3>

<table>
    <tr>
        <td width="30%">Invoice Retur</td>
        <td>{{ $retur->invoice_retur }}</td>
    </tr>
    <tr>
        <td>Invoice Sales</td>
        <td>{{ $retur->sale->invoice ?? '-' }}</td>
    </tr>
    <tr>
        <td>Divisi</td>
        <td>{{ $retur->divisi->name ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ dateId($retur->created_at) }}</td>
    </tr>
</table>

<br>

<b>Detail Item</b>
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
                {{ number_format($acc->subtotal / max($acc->qty,1)) }}
            </td>
            <td class="text-end">{{ number_format($acc->subtotal) }}</td>
        </tr>
    @endforeach
    <tfoot>
    <tr>
        <th colspan="4" class="text-center bg-light">Total Price</th>
        <th colspan="2" class="text-center bg-light">{{formatRupiah($retur->sale->total_price)}}</th>
    </tr>
    </tfoot>
    </tbody>
</table>


<br><br>
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

</body>
</html>
