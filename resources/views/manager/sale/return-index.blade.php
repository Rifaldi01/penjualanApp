@extends('layouts.master')

@section('content')

    <div class="card">

        <div class="card-header">

            <div class="row">

                <div class="col-md-6">

                    <h4 class="text-uppercase mt-2">
                        List Transaksi Retur
                    </h4>

                </div>

                <div class="col-md-6">

                    <form method="GET"
                          action="{{ route('manager.sale.return.index') }}">

                        <div class="row">

                            <div class="col-md-3">

                                <select name="year"
                                        class="form-control">

                                    <option value="all">
                                        Semua Tahun
                                    </option>

                                    @foreach($years as $year)

                                        <option value="{{ $year }}"
                                            {{ request('year', date('Y')) == $year ? 'selected' : '' }}>

                                            {{ $year }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <select name="month"
                                        class="form-control">

                                    <option value="all">
                                        Semua Bulan
                                    </option>

                                    @for($m = 1; $m <= 12; $m++)

                                        <option value="{{ $m }}"
                                            {{ request('month', date('m')) == $m ? 'selected' : '' }}>

                                            {{ date('F', mktime(0,0,0,$m,1)) }}

                                        </option>

                                    @endfor

                                </select>

                            </div>

                            <div class="col-md-3">

                                <select name="divisi_id"
                                        class="form-control">

                                    <option value="all">
                                        Semua Divisi
                                    </option>

                                    @foreach($divisi as $div)

                                        <option value="{{ $div->id }}"
                                            {{ request('divisi_id') == $div->id ? 'selected' : '' }}>

                                            {{ $div->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3">

                                <button class="btn btn-primary">
                                    Filter
                                </button>

                                <a href="{{ route('manager.sale.return.index') }}"
                                   class="btn btn-secondary">

                                    Reset

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped"
                       id="table-return">

                    <thead>

                    <tr>

                        <th>No</th>
                        <th>Tanggal Retur</th>
                        <th>Invoice Retur</th>
                        <th>Invoice Sale</th>
                        <th>Customer</th>
                        <th>Divisi</th>
                        <th>Total Retur</th>
                        <th>User</th>
                        <th>Keterangan</th>
                        <th width="5%">Action</th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($salesReturns as $key => $data)

                        <tr>

                            <td>{{ $key + 1 }}</td>

                            <td>
                                {{ tanggal($data->created_at) }}
                            </td>

                            <td>
                                {{ $data->return_invoice }}
                            </td>

                            <td>
                                {{ $data->sale->invoice ?? '-' }}
                            </td>

                            <td>
                                {{ $data->sale->customer->name ?? '-' }}
                            </td>

                            <td>
                                {{ $data->sale->divisi->name ?? '-' }}
                            </td>

                            <td>
                                {{ formatRupiah($data->total_return) }}
                            </td>

                            <td>
                                {{ $data->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $data->description }}
                            </td>
                            <td>

                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailReturn{{ $data->id }}">

                                    <i class="bx bx-show"></i>

                                </button>
                                <div class="modal fade" id="detailReturn{{ $data->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content border-0">
                                            <div class="modal-body p-4">

                                                {{-- HEADER --}}
                                                <div class="border p-4 mb-4 bg-light">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-2">
                                                            <img src=" {{ asset('images/logo/' . $data->sale->user->divisi->logo) }}"
                                                                 width="150">
                                                        </div>
                                                        <div class="col-md-10">
                                                            <h5 class="fw-bold">
                                                                Komplek Sukamenak Indah Blok Q90 Kopo Sayati
                                                            </h5>
                                                            <h5 class="fw-bold">
                                                                Sukamenak, Margahayu, Kabupaten Bandung 40227
                                                            </h5>
                                                            <h5 class="fw-bold">
                                                                Phone: (022) 5442 0354 | 082129900025
                                                            </h5>
                                                            <h5 class="fw-bold">
                                                                Email: dndsurvey90@gmail.com | Website : dndsurvey.id
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr style="height:4px;background:#999">

                                                {{-- TANGGAL --}}
                                                <div class="mb-4">
                                                    Bandung, {{ tanggal($data->created_at) }}
                                                </div>

                                                {{-- INFO RETUR --}}
                                                <table class="table table-bordered mb-5">
                                                    <tr style="background:#d0d3d6">
                                                        <td colspan="4" class="text-end">
                                                            <strong>SURAT RETUR PENJUALAN</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>Invoice</td>
                                                        <td>{{$data->sale->invoice}}</td>
                                                        <td width="10%">No. Return</td>
                                                        <td>
                                                            {{ $data->return_invoice }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td width="10%">Pelanggan</td>
                                                        <td width="50%">
                                                            {{ $data->sale->customer->name ?? '-' }}
                                                        </td>
                                                        <td>Perihal</td>
                                                        <td>Return</td>
                                                    </tr>
                                                </table>

                                                {{-- BARANG --}}
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th>Nama Barang</th>
                                                        <th width="15%">Jumlah</th>
                                                        <th width="20%">Kode</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @php $no = 1; @endphp

                                                    @foreach($data->returnItems as $item)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $item->itemSale->name ?? '-' }}</td>
                                                            <td>1</td>
                                                            <td>{{ $item->itemSale->no_seri ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach

                                                    @foreach($data->returnAccessories as $acc)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $acc->accessories->name ?? '-' }}</td>
                                                            <td>{{ $acc->qty }}</td>
                                                            <td>{{$acc->accessories->code_acces ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach

                                                    </tbody>
                                                </table>

                                                {{-- TTD --}}

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection

@push('js')

    <script>

        $(document).ready(function () {

            $('#table-return').DataTable();

        });

    </script>

@endpush
