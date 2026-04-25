@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6 mt-3">
                    <div class="container">
                        <h4 class="text-uppercase">Accessories Balance</h4>
                    </div>
                </div>
                <div class="col-6 mt-3">
                    <form method="GET">
                        <select name="year" onchange="this.form.submit()">

                            @for($y = now()->year; $y >= 2020; $y--)

                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected':'' }}>
                                    {{ $y }}
                                </option>

                            @endfor

                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Accessories</th>
                        <th>Barang Masuk</th>
                        <th>Barang Terjual</th>
                        <th>Barang Dikembalikan</th>
                        <th>Barang Rusak</th>
                        <th>Barang Diminta</th>
                        <th>Minta Barang</th>
                        <th>Sisa</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($data as $key => $row)

                        @php
                            $sisa = $row->barang_masuk
                            - $row->barang_terjual
                            + $row->barang_dikembalikan
                            - $row->barang_rusak
                            - $row->barang_diminta
                            + $row->minta_barang;
                        @endphp

                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $row->code_acces }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->barang_masuk }}</td>
                            <td>{{ $row->barang_terjual }}</td>
                            <td>{{ $row->barang_dikembalikan }}</td>
                            <td>{{ $row->barang_rusak }}</td>
                            <td>{{ $row->barang_diminta }}</td>
                            <td>{{ $row->minta_barang}}</td>
                            <td>{{ $sisa }}</td>
                        </tr>

                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')

@endpush
@push('js')

@endpush

