<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        @page {
            size: A4; /* Menentukan ukuran kertas A4 */
            margin: 20mm; /* Menentukan margin halaman */
        }
        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-spacing: 3px; /* Menghilangkan jarak antar sel */
        }

        td {
            width: 33.33%; /* Lebar setiap kolom sama, 1/3 dari tabel */
            height: 100px; /* Tinggi tetap untuk setiap sel */
            border: 1px solid #333; /* Border untuk setiap sisi */
            vertical-align: middle; /* Vertikal di tengah */
            padding: 10px; /* Memberikan ruang di dalam sel */
            text-align: center; /* Menjaga teks tetap rata tengah */
        }

        img {
            display: block;
            margin: 0 auto; /* Membuat gambar berada di tengah */
            margin-bottom: 10px; /* Memberikan ruang di bawah gambar */
        }

        .code {
            font-size: 1.1em; /* Ukuran font untuk kode */
            margin-bottom: 5px;
            word-wrap: break-word;
            display: inline-block;
            letter-spacing: 6.4px /* Memberikan ruang di bawah kode akses */
        }

        .price {
            background-color: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            padding: 5px;
            font-size: 0.9em; /* Ukuran font untuk harga */
            border-radius: 3px; /* Sedikit melengkung di sudut */
            margin-top: 5px; /* Memberikan ruang di atas harga */
        }
        .bg{
            background-color: black ;
            color: white;
            width: 170px;

        }
    </style>
</head>
<body>
<table>
    <tr>
        @foreach ($items as $item)
            <td class="text-center">
                <img src="{{ $barcodePath[$item->id] }}" alt="{{ $item->name }}" width="170" height="60">
                <div class="code bg">{{ $item->no_seri }}</div>
            </td>
            @if ($no++ % 3 == 0)
    </tr><tr>
        @endif
        @endforeach
    </tr>
</table>
</body>
</html>
