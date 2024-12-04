<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>

    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-spacing: 3px;
        }

        td {
            width: 33.33%;
            height: 100px;
            border: 1px solid #333;
            vertical-align: middle;
            padding: 10px;
            text-align: center;
        }

        img {
            display: block;
            margin: 0 auto;
            margin-bottom: 10px;
        }

        .code {
            font-size: 1.1em;
            margin-bottom: 5px;
            word-wrap: break-word;
            display: inline-block;
            letter-spacing: 6.4px;
        }

        .bg{
            background-color: black;
            color: white;
            width: 170px;
        }
    </style>
</head>
<body>
<table>
    <tr>
        @foreach ($accessories as $accessory)
            @foreach ($barcodePath[$accessory->id] as $barcodeFile)
                <td class="text-center">
                    <img src="{{ $barcodeFile }}" alt="{{ $accessory->name }}" width="170" height="60">
                    <div class="code bg">{{ $accessory->code_acces }}</div>
                </td>
                @if ($no++ % 3 == 0)
    </tr><tr>
        @endif
        @endforeach
        @endforeach
    </tr>
</table>
</body>
</html>
