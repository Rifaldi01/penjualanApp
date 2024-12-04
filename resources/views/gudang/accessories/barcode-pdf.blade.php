<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-spacing: 3px;
            table-layout: fixed;
        }

        td {
            width: 10%;
            height: auto;
            border: 1px solid #333;
            vertical-align: middle;
            padding: 10px;
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
            margin-bottom: 10px;
            max-width: 100%; /* Membatasi lebar gambar barcode */
            height: auto;   /* Menjaga proporsi gambar */
        }

        .barcode {
            display: block;
            margin: 0 auto;
            width: 100%;
        }

        .code {
            font-size: 1.1em;
            margin-top: 10px;
            word-wrap: break-word;
            display: inline-block;
            letter-spacing: 6.4px;
            background-color: black;
            color: white;
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<table>
    <tr>
        @php $no = 1; @endphp
        @foreach ($accessories as $accessory)
            @foreach ($barcodePath[$accessory->id] as $barcodeFile)
                <td>
                    <div class="barcode">
                        {!! $barcodeFile !!}
                    </div>
                    <div class="code">{{ $accessory->code_acces }}</div>
                </td>
                @if ($no++ % 4 == 0)
    </tr><tr>
        @endif
        @endforeach
        @endforeach
    </tr>
</table>
</body>
</html>
