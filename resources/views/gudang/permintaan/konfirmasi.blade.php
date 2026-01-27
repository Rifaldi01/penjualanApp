@extends('layouts.master')
@section('content')
   <div class="card">
       <div class="container">
          <div class="card-header mb-3 mt-3">
              <h4>DAFTAR PERMINTAAN</h4>
          </div>
           <div class="card-body">
               <table class="table table-bordered table-striped" id="example2">
                   <thead>
                   <tr>
                       <th width="2%">No</th>
                       <th>Tanggal</th>
                       <th>Kode</th>
                       <th>Barang</th>
                       <th>Kode Acces</th>
                       <th>Qty</th>
                       <th>Divisi Tujuan</th>
                       <th width="2%">Jumlah Barang</th>
                       <th width="2%">Status</th>
                       <th width="2%">Aksi</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($permintaans as $key => $permintaan)
                       <tr>
                           <td>{{$key +1}}</td>
                           <td>{{ tanggal($permintaan->created_at) }}</td>
                           <td>{{ $permintaan->kode }}</td>
                           <td>
                               @forelse($permintaan->detailAccessories as $detail)
                                   {{ $detail->accessories?->name ?? '-' }}<br>
                               @empty
                                   -
                               @endforelse
                           </td> <td>
                               @forelse($permintaan->detailAccessories as $detail)
                                   {{ $detail->accessories?->code_acces ?? '-' }}<br>
                               @empty
                                   -
                               @endforelse
                           </td>
                           <td>
                               @forelse($permintaan->detailAccessories as $accessory)
                                   <li>{{ $accessory->qty ?? '-' }}</li>
                               @empty
                                   -
                               @endforelse
                           </td>

                           <td>{{ $permintaan->divisiTujuan->name }}</td>
                           <td>{{ $permintaan->jumlah }}</td>
                           <td>
                               @if($permintaan->status == 'pending')
                                   <span class="badge bg-warning text-dark">Pending</span>
                               @elseif($permintaan->status == 'disetujui')
                                   <span class="badge bg-success ">Disetujui</span>
                               @else
                                   <span class="badge bg-primary ">Diterima</span>
                               @endif
                           </td>
                           <td>
                               <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"
                                       data-bs-target="#exampleExtraLargeModal{{$permintaan->id}}" data-bs-tool="tooltip"
                                       data-bs-placement="top" title="Print Surat Jalan">
                               </button>
                               @if(Auth::user()->divisi_id == $permintaan->divisi_id_asal && $permintaan->status == 'pending')
                                   <!-- Tombol Terima -->
                                   <form action="{{ route('gudang.permintaan.receive', $permintaan->id) }}" method="POST">
                                       @csrf
                                       @method('PUT')
                                       <button type="submit" class="btn btn-dnd btn-sm bx bx-check" data-bs-toggle="tooltip" data-bs-placement="top" title="Setujui Permintaan"></button>
                                   </form>
                               @endif
                               @if(Auth::user()->divisi_id == $permintaan->divisi_id_tujuan && $permintaan->status == 'disetujui' || $permintaan->status == 'diterima' )
{{--                                       <button class="btn btn-dnd lni lni-files btn-sm" data-bs-toggle="modal"--}}
{{--                                               data-bs-target="#exampleExtraLargeModal{{$permintaan->id}}" data-bs-tool="tooltip"--}}
{{--                                               data-bs-placement="top" title="Print Surat Jalan">--}}
{{--                                       </button>--}}

                               @endif
                                   @include('gudang.permintaan.surat-penerimaan')

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
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endpush
