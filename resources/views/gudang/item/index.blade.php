@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="row">
                <div class="col-6">
                    <div class="container mt-3">
                        <h4 class="text-uppercase">List Items</h4>
                    </div>
                </div>
                <div class="col-6">
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Add Item"
                       href="{{route('gudang.item.create')}}" class="btn btn-dnd float-end me-3 mt-3 btn-sm shadow"><i
                            class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="box-header with-border">
                <!-- Form untuk cetak barcode -->
                <form id="printBarcodeForm" action="{{ route('gudang.item.print') }}" method="POST" target="_blank">
                    @csrf
                    <div class="btn-group mb-1">
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="bx bx-barcode"></i> Print Barcode
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table id="exampleA" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="5%">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th width="2%">No</th>
                        <th>Nama Item</th>
                        <th>No Seri</th>
                        <th>Price</th>
                        <th class="text-center" width="5%">Jumalah Barcode</th>
                        <th class="text-center" width="5%">Status</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $key => $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="items[]" value="{{ $item->id }}" class="select_item" form="printBarcodeForm">
                            </td>
                            <td>{{$key + 1}}</td>
                            <td>
                                <a class="text-dark">{{$item->name}}</a>
                            </td>
                            <td>{{$item->cat->name}}-{{$item->no_seri}}</td>
                            <td>{{formatRupiah($item->price)}},-</td>
                            <td class="text-center">
                                <input type="number" class="form-control" value="1" readonly>
                            </td>
                            <td>
                                @if($item->status == 0)
                                    <span class="badge bg-success">Redy</span>
                                @else
                                    <span class="badge bg-danger">Reject</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('gudang.item.destroy', $item->id)}}" data-confirm-delete="true"
                                   class="btn btn-danger btn-sm bx bx-trash" data-bs-toggle="tooltip"
                                   data-bs-placement="top" title="Delete">
                                </a>
                                <a href="{{route('gudang.item.edit', $item->id)}}"
                                   class="btn btn-sm btn-dnd bx bx-edit me-1"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                </a>
                                @if($item->status == 0)
                                    <a href="#"
                                       class="btn btn-warning btn-sm bx bx-error"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Item Reject"
                                       onclick="confirmReject('{{ route('gudang.item.reject', $item->id) }}')">
                                    </a>
                                @else
                                    <a href="#"
                                       class="btn btn-success btn-sm bx bx-check"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Item Redy"
                                       onclick="confirmRedy('{{ route('gudang.item.redy', $item->id) }}')">
                                    </a>
                                @endif
                            </td>
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
    <script>
        // Checkbox untuk memilih semua accessories
        document.getElementById('select_all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.select_item');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        $(document).ready(function () {
            $('#exampleA').DataTable({
                lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
                pageLength: 10, // Default halaman pertama
                responsive: true, // Untuk tampilan responsif
            });
        });

        //alert comfirm
        function confirmReject(url) {
            event.preventDefault(); // Mencegah aksi default link

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Item yang direject tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reject!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat dan submit form secara dinamis
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        function confirmRedy(url) {
            event.preventDefault(); // Mencegah eksekusi default link

            Swal.fire({
                title: 'Apakah Item Redy?',
                text: "Item yang Redy bisa dijual!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#71dd33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Redy!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat dan submit form secara dinamis
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
