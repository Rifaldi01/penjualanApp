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

        body {
            margin: 0;
            font-family: Arial, sans-serif;
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

        .barcode {
            display: block;
            margin: 0 auto;
            max-width: 100%; /* Membatasi lebar maksimal */
            max-height: 80px; /* Membatasi tinggi maksimal */
            object-fit: contain; /* Menjaga proporsi barcode */
        }

        .code {
            font-size: 1em;
            margin-top: 10px;
            word-wrap: break-word;
            display: inline-block;
            letter-spacing: 4px;
            background-color: black;
            color: white;
            padding: 5px;
            border-radius: 3px;
        }

        /* Responsiveness */
        @media (max-width: 1024px) {
            td {
                padding: 5px;
            }

            .code {
                font-size: 0.9em;
                letter-spacing: 3px;
            }
        }

        @media (max-width: 768px) {
            td {
                padding: 3px;
                font-size: 0.8em;
            }

            .code {
                font-size: 0.8em;
                letter-spacing: 2.5px;
            }
        }

        @media (max-width: 480px) {
            td {
                padding: 2px;
                font-size: 0.7em;
            }

            .code {
                font-size: 0.7em;
                letter-spacing: 2px;
            }
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
