<div class="modal fade" id="exampleLargeModal{{$data->id}}" tabindex="-1"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="invoice overflow-auto" id="printable{{$data->id}}">
                <!-- Konten modal -->
                <div class="modal-body modal-invoice">
                    <!-- Konten modal seperti tabel, gambar, dll. -->
                    <table>
                        <tr>
                            <td rowspan="4" style="border: none; width: 120px; vertical-align: middle;">
                                <img src="{{ asset('images/logo/' . $data->divisi->logo) }}"
                                     class="print-img"
                                     alt="dnd logo"
                                     style="width: 100%; height: 100px; object-fit: contain;">
                            </td>
                            <td style="border: none; padding-left: 15px; white-space: nowrap;">
                                <strong style="font-size: 12px;">{{$data->divisi->alamat}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 15px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Sukamenak, Margahayu, Kabupaten Bandung 40227</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 15px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Phone: {{$data->divisi->phone}}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-left: 15px; white-space: nowrap;">
                                <strong style="font-size: 12px;">Email: {{$data->divisi->email}}</strong>
                            </td>
                        </tr>
                    </table>

                    <div class="row">
                        {{--                        <div class="col-lg-6">--}}
                        {{--                            <img src="{{asset('images/logo/'. $data->divisi->logo)}}"--}}
                        {{--                                 class="float-start print-img" alt="dnd logo">--}}
                        {{--                        </div>--}}
                        {{--                        <div class="float-start ms-3"><strong style="font-size: 13px;">{{$data->divisi->name}}</strong></div>--}}
                        {{--                        <div class="float-start ms-3"></div>--}}
                        {{--                        <div class="float-start ms-3"></div>--}}
                        {{--                        <div class="float-start ms-3"></div>--}}
                        {{--                        <div class="float-start ms-3"></div>--}}
                    </div>
                    <hr>
                    <table class="table-style">
                        <tr>
                            <td>Invoice To</td>
                            <td width="13%" class="text-start">No Invoice</td>
                            <td width="2%" class="text-center">:</td>
                            <td>
                                <strong>{{ $data->invoice }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{$data->customer->name}}</strong> <br>
                                <strong>{{$data->customer->addres}}</strong>
                            </td>
                            <td width="10%" class="text-start">No PO</td>
                            <td width="2%" class="text-center">:</td>
                            <td>
                                @if($data->no_po)
                                    {{$data->no_po}}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{$data->customer->company}}</strong>
                            </td>
                            <td width="10%" class="text-start">Tanggal</td>
                            <td width="2%" class="text-center">:</td>
                            <td>{{tanggal($data->created_at)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{$data->customer->company}}</strong>
                            </td>
                            <td class="text-start">No. Rekening</td>
                            <td width="1%" class="text-end">:</td>
                            <td width="20%">{{$data->customer->divisi->no_rek}}</td>
                        </tr>
                    </table>
                    <hr>
                    <!-- Tabel detail item -->
                    <table class="table table-bordered">
                        <thead>
                        <thead>
                        <tr>
                            <th colspan="5" class="text-center bg-secondary bg-opacity-50 sjg" style="font-size: 13px;">
                                <strong>INVOICE</strong>
                            </th>
                        </tr>
                        <tr>
                            <th width="1%">No</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th class="text-center">Qty</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data->itemSales as $key => $item)
                            <tr>
                                <td width="1%" class="text-center">{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-right">{{ formatRupiah($item->price) }}</td>
                                <td class="text-center">1</td>
                                <td class="text-right">{{ formatRupiah($item->price) }}</td>
                            </tr>
                        @endforeach
                        @foreach($data->accessoriesSales as $key => $accessories)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $accessories->accessories->name }}</td>
                                <td class="text-right">{{ formatRupiah($accessories->subtotal / $accessories->qty) }}</td>
                                <td class="text-center">{{ $accessories->qty }}</td>
                                <td class="text-right">{{ formatRupiah($accessories->subtotal) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mb-lg-5">
                        <div class="row">
                            <div class="col-lg-6"></div>
                            <div class="col-lg-6">
                                <div class="float-end">
                                    <table>
                                        <tr>
                                            <td style="vertical-align: top;">
                                                <table class="table table-bordered">
                                                    @if($data->nominal_in == $data->pay)
                                                    @else
                                                        <tr>
                                                            <td colspan="4" style="font-size: 10px"><strong>DIBAYAR</strong></td>
                                                            <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->nominal_in) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" style="font-size: 10px"><strong>TAGIHAN</strong></td>
                                                            <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->pay - $data->nominal_in) }}</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </td>
                                            <td>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>SUBTOTAL</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->total_price) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>PPN</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->ppn) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>PPH</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->pph) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>ONGKIR</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->ongkir) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>DISCOUNT</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->diskon) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="font-size: 10px"><strong>GRAND TOTAL</strong></td>
                                                        <td class="text-right" style="font-size: 10px">{{ formatRupiah($data->pay) }}</td>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <table class="table">
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td class="text-center" style="border: none;">Hormat Kami,</td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                                <td class="text-center" width="20%" height="70%" style="border: none;">
                                    {{Auth::user()->name}} <br>
                                </td>
                            </tr>
                        </table>
                        {{--                        <div class="row">--}}
                        {{--                            <div class="col-lg-4"></div>--}}
                        {{--                            <div class="col-lg-4"></div>--}}
                        {{--                            <div class="col-lg-4">--}}
                        {{--                                <div class="mb-2 text-center">Hormat Kami,</div>--}}
                        {{--                                <div class="text-center">--}}
                        {{--                                    <br>--}}
                        {{--                                    <br>--}}
                        {{--                                    <br>--}}
                        {{--                                    <img src="{{asset('images/dnd-ttd.png')}}"--}}
                        {{--                                         class="print-img" alt="dnd logo">--}}
                        {{--                                </div>--}}
                        {{--                                <div--}}
                        {{--                                    class="mt-2 text-center">{{Auth::user()->name}}</div>--}}
                        {{--                                <div class="text-center">Administration</div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        onclick="printModalContent('printable{{$data->id}}')">
                    Print
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
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

            td {
                font-size: 12px;
            }

        }

        .print-img {
            width: 38%;
        }

        .modal-invoice {
            padding: 15mm;
        }
    </style>
@endpush
@push('js')
    <script>
        function printModalContent(modalId) {
            var printContents = document.getElementById(modalId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // Reload untuk mengembalikan tampilan asli
        }
    </script>
@endpush
