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
            border-spacing: 5px;
            table-layout: fixed;
        }

        td {
            width: 10%;
            height: auto;
            border: 0.5px solid #333;
            vertical-align: middle;
            padding: 10px;
            text-align: center;
        }

        .barcode {
            resize: both;
            overflow: auto;
        }

        .code {
            font-size: 1em;
            margin-top: 10px;
            background-color: black;
            color: white;
            padding: 3px;
            border-radius: 3px;

            /* Flexbox for spacing */
            display: flex;
            justify-content: center;
            gap: 15px; /* Adjust gap to control spacing */
        }

        .code span {
            display: inline-block;
        }

        /* Responsiveness */
        @media (max-width: 1024px) {
            td {
                padding: 5px;
            }

            .code {
                font-size: 0.9em;
                letter-spacing: 6px;
            }
        }

        @media (max-width: 768px) {
            td {
                width: 4.8cm;
                height: 1.5cm;
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
                    <div class="code">
                        @foreach (str_split($accessory->code_acces) as $char)
                            <span>{{ $char }}</span>
                        @endforeach
                    </div>
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
