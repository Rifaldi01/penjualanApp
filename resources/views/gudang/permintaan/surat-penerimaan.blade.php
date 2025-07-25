<div class="modal fade" id="exampleExtraLargeModal{{$permintaan->id}}" tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Surat Penerimaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="invoice overflow-auto" id="prinsurat{{$permintaan->id}}">
                <div class="modal-body modal-surat">
                    <table>
                        <tr>
                            <td rowspan="4" style="border: none; width: 120px; vertical-align: middle;">
                                <img src="{{asset('images/logo/'. Auth::user()->divisi->logo)}}"
                                     class="print-img"
                                     alt="dnd logo"
                                     style="width: 100%; height: 100px; object-fit: contain;">
                            </td>
                            <td style="border: none; padding-left: 12px; white-space: nowrap;">
                                <strong style="font-size: 12px;">{{Auth::user()->divisi->alamat}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 12px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Sukamenak, Margahayu, Kabupaten Bandung 40227</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 12px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Phone: {{Auth::user()->divisi->phone}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 12px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Email: {{Auth::user()->divisi->email}}</strong>
                            </td>
                        </tr>
                    </table>
{{--                    <div class="text-center">--}}
{{--                        <img src="{{asset('images/logo/'. Auth::user()->divisi->logo)}}" alt="" width="24%" class="img-surat">--}}
{{--                        <div class="mt-1"><strong style="font-size: 12px;">Komplek--}}
{{--                                Sukamenak Indah Blok Q90 Kopo - Sayati, Kabupaten Bandung,</strong>--}}
{{--                        </div>--}}
{{--                        <div class="mt-1"><strong style="font-size: 12px;">Website : dndsurvey.id |--}}
{{--                                Email : admin@dndsurvey.id</strong>--}}
{{--                        </div>--}}
{{--                        <div class="mt-1"><strong style="font-size: 12px;">Kantor . 022 - 5442 0354--}}
{{--                                /Phone. 0821-2990-0025 / 081-2992-5005</strong>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <hr style="border: 3px solid #000">
                    <div class="mb-3">
                        <strong>Bandung,</strong> {{tanggal($permintaan->created_at)}}
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="" style="width:100%">
                            <thead>
                            <tr>
                                <th colspan="6" class="text-end bg-secondary bg-opacity-50 sjg" style="font-size: 18px;">
                                    SURAT PENERIMAAN BARANG
                                </th>
                            </tr>
                            <tr>
                                <th width="4%">Kepada</th>
                                <th width="1%">:</th>
                                <th>{{$permintaan->divisiTujuan->name}}</th>
                                <th width="1%">No</th>
                                <th width="1%" class="text-end">:</th>
                                <th width="1%" class="text-end"
                                    style="border-right-width:0;">{{ $permintaan->kode }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th width="1%" class="text-end">Perihal</th>
                                <th width="1%" class="text-end">:</th>
                                <th width="1%" style="border-right-width:0;">Permintaan Accessories</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center" width="1%" style="border-left-width:1px;">No</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permintaan->detailAccessories as $key => $item)
                                <tr>
                                    <td width="1%" class="text-center" style="border-left-width:1px;">{{ $key + 1 }}</td>
                                    <td>{{ $item->accessories?->name ?? '-' }}</td>
                                    <td class="text-right">{{ $item->qty ?? '-' }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <th colspan="2">
                                    <div class="float-end">
                                        Jumlah Barang
                                    </div>
                                </th>
                                <th>{{$permintaan->jumlah}}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        <table width="100%">
                            <thead>
                            <tr>
                                <th class="text-center">Penyerah Barang,</th>
                                <th class="text-center"></th>
                                <th class="text-center" style="border-right-width:0px;">Penerima Barang,</th>
                            </tr>
                            </thead>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td style="border-right-width:0px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td style="border-right-width:0px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td style="border-right-width:0px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="text-center">(...........................)</td>
                                <td class="text-center"></td>
                                <td class="text-center" style="border-right-width:0px;">(...........................)</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="printSuratJalan('prinsurat{{$permintaan->id}}')">
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('head')
    <style>
        /* CSS khusus untuk print */
        @media print {

            @page {
                size: A4;
                margin: 0;
            }

            .table-style {
                width: 100%;
                margin-top: 4px;
                margin-bottom: 4px;
                border-bottom-width: 0;
            }

            .modal-dialog,
            .modal-dialog * {
                visibility: visible;
            }

            .modal-dialog {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }

        th.sjg {
            background-color: rgba(108, 117, 125, 0.5) !important; /* bg-secondary bg-opacity-50 */
            -webkit-print-color-adjust: exact; /* Forcing color to be printed */
            color-adjust: exact; /* Forcing color to be printed in Firefox */
        }

        .img-surat {
            width: 38%;
        }

        .modal-surat {
            padding: 15mm;
            font-size: 12px;
        }
    </style>
@endpush
@push('js')
    <script>
        function printSuratJalan(modalId) {
            var printContents = document.getElementById(modalId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // Reload untuk mengembalikan tampilan asli
        }
    </script>
@endpush
