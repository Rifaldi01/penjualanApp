@extends('layouts.master')
@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                @if(isset($item))
                    <h3 class="mb-4 ms-3">Edit Item<i class="bx bx-edit"></i></h3>
                @else
                    <h3 class="mb-4 ms-3">Add Item<i class="bx bx-user-plus"></i></h3>
                @endif
                <hr>
            </div>

        </div>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-danger"><i class='bx bxs-message-square-x'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-danger">Error</h6>
                            <div>
                                <div>{{ $error }}</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        @endif
        <form class="card-body p-4" action="{{route('admin.item.update', $item->id)}}" method="POST"
              enctype="multipart/form-data" id="myForm">
            @csrf
            @isset($item)
                @method('PUT')
            @endisset
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-2">
                        <label class="col-form-label">Name Item</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Namae Item"
                               value="{{isset($item) ? $item->name : null}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="col-form-label">Tanggal Masuk</label>
                        <input type="text" name="created_at" class="form-control datepicker" placeholder="Enter Date"
                               value="{{isset($item) ? $item->created_at : null}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="col-form-label">Invoice</label>
                        <input type="text" name="kode_msk" class="form-control" placeholder="Enter Invoice"
                               value="{{ isset($item) ? optional($item->itemIn)->kode_msk : null }}">
                    </div>
                </div>
            </div>
            <div class="mt-3 mb-2">
                <label for="single-select-field" class="form-label">Category</label>
                <select name="itemcategory_id" class="form-control" id="category"
                        data-placeholder="Pilih Category">
                    @foreach($cat as $category)
                        @if(isset($item))
                            <option
                                value="{{ $category->id }}" {{ $item->itemcategory_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @else
                            <option value=""></option>
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="mt-3 mb-2">
                <label class="col-form-label">No Seri</label>
                <input type="text" name="no_seri" class="form-control" placeholder="Enter No Seri"
                       value="{{isset($item) ? $item->no_seri : null}}">
            </div>
            <div class="mt-3 mb-2">
                <label class="col-form-label">Capital Price</label>
                <div class="input-group"><span class="input-group-text" id="basic-addon1">Rp.</span>
                    <input type="text" name="capital_price" class="form-control" onkeyup="formatRupiah(this)"
                           value="{{isset($item) ? $item->capital_price : null}}" placeholder="0">
                </div>
            </div>
            <div class="mb-2">
                <label class="col-form-label">Harga Jual</label>
                <div class="input-group"><span class="input-group-text" id="basic-addon1">Rp.</span>
                    <input type="text" name="price" class="form-control" onkeyup="formatRupiah(this)"
                           value="{{isset($item) ? $item->price : null}}" placeholder="0">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-dnd float-end" id="submitBtn">Save<i
                            class="bx bx-save"></i></button>

                        <a href="{{route('admin.item.editItem')}}" class="btn btn-warning float-end me-2"><i
                                class="bx bx-undo"></i>Back</a>

                </div>
            </div>
        </form>
    </div>
@endsection

@push('head')

@endpush
@push('js')
    <script>
        $(document).ready(function () {
            $('#submitBtn').click(function () {
                // Disable button dan ubah teksnya
                $(this).prop('disabled', true).text('Loading...');

                // Kirim form secara manual
                $('#myForm').submit();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#category').select2({
                theme: 'bootstrap-5',
                placeholder: "--Pilih Invoice--",
                width: '100%'
            });
        });
    </script>
@endpush
