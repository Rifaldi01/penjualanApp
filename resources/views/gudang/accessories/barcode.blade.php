@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                <h3>Tambah / Edit Accessories Multiple</h3>
                <hr>
            </div>
        </div>

        <div class="card-body">

            <form action="{{ $url }}" method="POST" id="myFormAcces">
                @csrf

                @isset($acces)
                    @method('PUT')
                @endisset

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="45%">Nama Accessories</th>
                        <th width="35%">Region</th>
                        <th width="20%">Action</th>
                    </tr>
                    </thead>

                    <tbody id="table-body">

                    @if(isset($items) && count($items))

                        @foreach($items as $row)
                            <tr>
                                <td>
                                    <input type="hidden" name="id[]" value="{{ $row->id }}">
                                    <input type="text"
                                           name="name[]"
                                           class="form-control"
                                           value="{{ $row->name }}">
                                </td>

                                <td>
                                    <select name="region[]" class="form-control region">
                                        <option value="">-- Pilih --</option>
                                        <option value="Dalam Negeri"
                                            {{ $row->region == 'Dalam Negeri' ? 'selected' : '' }}>
                                            DN
                                        </option>
                                        <option value="Luar Negeri"
                                            {{ $row->region == 'Luar Negeri' ? 'selected' : '' }}>
                                            LN
                                        </option>
                                    </select>
                                </td>

                                <td>
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            onclick="removeRow(this)">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                    @else

                        <tr>
                            <td>
                                <input type="hidden" name="id[]" value="">
                                <input type="text"
                                       name="name[]"
                                       class="form-control"
                                       placeholder="Nama Accessories">
                            </td>

                            <td>
                                <select name="region[]" class="form-control region">
                                    <option value="">-- Pilih --</option>
                                    <option value="Dalam Negeri">DN</option>
                                    <option value="Luar Negeri">LN</option>
                                </select>
                            </td>

                            <td>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="removeRow(this)">
                                    Hapus
                                </button>
                            </td>
                        </tr>

                    @endif

                    </tbody>
                </table>

                <button type="button" class="btn btn-success btn-sm" id="addRow">
                    + Tambah Row
                </button>

                <button type="submit" class="btn btn-primary btn-sm float-end" id="submitBtnAcces">
                    Simpan
                </button>

            </form>

        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            initSelect2();

            $('#addRow').click(function () {

                let row = `
        <tr>
            <td>
                <input type="hidden" name="id[]" value="">
                <input type="text" name="name[]" class="form-control">
            </td>

            <td>
                <select name="region[]" class="form-control region">
                    <option value="">-- Pilih --</option>
                    <option value="Dalam Negeri">DN</option>
                    <option value="Luar Negeri">LN</option>
                </select>
            </td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="removeRow(this)">
                    Hapus
                </button>
            </td>
        </tr>
        `;

                $('#table-body').append(row);

                initSelect2();
            });

            $('#submitBtnAcces').click(function () {
                $(this).prop('disabled', true).text('Loading...');
                $('#myFormAcces').submit();
            });

        });

        function removeRow(btn)
        {
            $(btn).closest('tr').remove();
        }

        function initSelect2()
        {
            $('.region').select2({
                theme:'bootstrap-5',
                width:'100%',
                placeholder:'-- Pilih Region --'
            });
        }
    </script>
@endpush
