@extends('layouts.master')

@section('content')

    <h3>List Stok Item</h3>
    <hr>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">

        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Items</p>
                            <h4 class="my-1 text-warning">{{$item}}</h4>
                            <p class="mb-0 font-13">All Items</p>
                        </div>

                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto">
                            <i class='bx bxs-box'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($itemsByCategory as $data)
            <div class="col">
                <div class="card radius-10 border-start border-0 border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">
                                    {{$data->cat->name ?? '-'}}
                                </p>

                                <h4 class="my-1 text-warning">
                                    {{ $data->total }}
                                </h4>

                                <small class="text-success">
                                    Ready : {{ $data->available }}
                                </small>
                            </div>

                            <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto">
                                <i class='bx bx-box'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <div class="card">
        <div class="card-header">

            <div class="row">
                <div class="col-sm-6">
                    <div class="mt-2">
                        <h3>TRANSAKSI {{$year}}</h3>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mt-2">
                        @if(auth()->user()->divisi_id == 1)
                            <form method="GET" action="{{ url()->current() }}">
                                <div class="row">

                                    <div class="col-md-4">
                                        <label>Tahun</label>
                                        <select name="year" class="form-select">
                                            @for($i = now()->year; $i >= 2020; $i--)
                                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Divisi</label>
                                        <select name="divisi_id" class="form-select">
                                            <option value="">Semua Divisi</option>

                                            @foreach($divisis as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ $divisi == $d->id ? 'selected' : '' }}>
                                                    {{ $d->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-end">
                                        <button class="btn btn-primary">
                                            Filter
                                        </button>
                                    </div>

                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">

                    <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th class="text-center">Tanggal</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-center">Total Price</th>
                        <th class="text-center">Diskon</th>
                        <th class="text-center">Ongkir</th>
                        <th class="text-center">Total Pay</th>
                        <th>Kasir</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($sales as $key => $data)
                        <tr>

                            <td>{{$key +1}}</td>
                            <td>{{tanggal($data->created_at)}}</td>
                            <td>{{$data->invoice}}</td>
                            <td>{{$data->customer->name ?? 'kosong'}}</td>
                            <td class="text-center">{{$data->total_item}}</td>
                            <td>{{formatRupiah($data->total_price)}}</td>
                            <td>{{formatRupiah($data->diskon)}}</td>
                            <td>{{formatRupiah($data->ongkir)}}</td>
                            <td>{{formatRupiah($data->pay)}}</td>
                            <td>{{$data->user->name ?? '-'}}</td>

                            <td class="text-center">

                                <button class="btn btn-dnd lni lni-files btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleExtraLargeModal{{$data->id}}">
                                </button>

                                @include('admin.sale.surat-jalan')

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

            $('#example').DataTable();

        });
    </script>
@endpush
