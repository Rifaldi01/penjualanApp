@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">Managemen Error</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaction"
                       class="table table-striped table-bordered"
                       style="width:100%">

                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Total Item</th>
                        <th>Total Haga</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>Diskon</th>
                        <th>Ongkir</th>
                        <th>Biaya Admin</th>
                        <th>Total Bayar</th>
                        <th>Uang Masuk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($sale as $key => $data)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ tanggal($data->created_at) }}</td>
                            <td>{{ $data->invoice }}</td>
                            <td>{{ optional($data->customer)->name ?? '-' }}</td>
                            <td>{{ $data->total_item }}</td>
                            <td>{{ $data->total_price }}</td>
                            <td>{{ $data->ppn }}</td>
                            <td>{{ $data->pph }}</td>
                            <td>{{ $data->diskon }}</td>
                            <td>{{ $data->ongkir }}</td>
                            <td>{{ $data->admin_fee }}</td>
                            <td>{{ $data->pay }}</td>
                            <td>{{ $data->nominal_in }}</td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm bx bx-edit btn-edit"
                                    data-id="{{ $data->id }}"
                                    data-total_price="{{ $data->total_price }}"
                                    data-ppn="{{ $data->ppn }}"
                                    data-pph="{{ $data->pph }}"
                                    data-diskon="{{ $data->diskon }}"
                                    data-ongkir="{{ $data->ongkir }}"
                                    data-admin_fee="{{ $data->admin_fee }}"
                                    data-pay="{{ $data->pay }}"
                                    data-nominal_in="{{ $data->nominal_in }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exampleVerticallycenteredModal"
                                    title="Edit">
                                </button>
                            </td>
                        </tr>

                    @endforeach

                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleVerticallycenteredModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <label>Total Harga</label>
                        <input type="text" id="total_price" name="total_price" class="form-control">

                        <label>PPN</label>
                        <input type="text" id="ppn" name="ppn" class="form-control">

                        <label>PPH</label>
                        <input type="text" id="pph" name="pph" class="form-control">

                        <label>Diskon</label>
                        <input type="text" id="diskon" name="diskon" class="form-control">

                        <label>Ongkir</label>
                        <input type="text" id="ongkir" name="ongkir" class="form-control">

                        <label>Biaya Admin</label>
                        <input type="text" id="admin_fee" name="admin_fee" class="form-control">

                        <label>Total Bayar</label>
                        <input type="text" id="pay" name="pay" class="form-control">

                        <label>Uang Masuk</label>
                        <input type="text" id="nominal_in" name="nominal_in" class="form-control">

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Account</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a href="{{route('superadmin.account.create')}}"
                       class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"><i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($user as $key => $data)
                            @foreach($data->roles as $role)
                                @if($data->roles == !null)
                                    <td>{{$key +1}}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$role->name}}</td>
                                    <td class="text-center user-status-{{ $data->id }}">
                                        @if($data->isOnline())
                                            <span class="badge bg-success">Online</span>
                                        @else
                                            <span class="badge bg-danger">Offline</span>
                                        @endif
                                    </td>

                    </tr>
                    @endif
                    @endforeach
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
    <script>
        $(document).ready(function () {
            var table = $('#transaction').DataTable();

            // Mengurutkan ulang nomor saat tabel diurutkan atau difilter
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.cells(null, 0, {search: 'applied', order: 'applied'}).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const buttons = document.querySelectorAll('.btn-edit');

            buttons.forEach(function(button){

                button.addEventListener('click', function(){

                    // Action form
                    let url = "{{ route('superadmin.error.update', ':id') }}";
                    url = url.replace(':id', this.dataset.id);

                    document.getElementById('editForm').action = url;

                    // Isi data
                    document.getElementById('total_price').value = this.dataset.total_price;
                    document.getElementById('ppn').value = this.dataset.ppn;
                    document.getElementById('pph').value = this.dataset.pph;
                    document.getElementById('diskon').value = this.dataset.diskon;
                    document.getElementById('ongkir').value = this.dataset.ongkir;
                    document.getElementById('admin_fee').value = this.dataset.admin_fee;
                    document.getElementById('pay').value = this.dataset.pay;
                    document.getElementById('nominal_in').value = this.dataset.nominal_in;

                });

            });

        });
    </script>
    <script>
        setInterval(function () {

            $.ajax({
                url: "{{ route('superadmin.user.status') }}",
                type: "GET",
                success: function (users) {

                    users.forEach(function(user){

                        let html = '';

                        if(user.online){
                            html = '<span class="badge bg-success">Online</span>';
                        }else{
                            html = '<span class="badge bg-danger">Offline</span>';
                        }

                        $('.user-status-' + user.id).html(html);

                    });

                }
            });

        }, 3000);
    </script>
@endpush

