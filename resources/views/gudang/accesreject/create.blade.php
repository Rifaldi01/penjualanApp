@extends('layouts.master')

@section('content')
   <div class="card">
       <div class="container">
           <div class="card-header">
               <h4>Tambah Accessories Reject</h4>
           </div>
           <div class="card-body">
               <!-- Menampilkan notifikasi success -->
               @if(session('success'))
                   <div class="alert alert-success">
                       {{ session('success') }}
                   </div>
               @endif

               <!-- Form untuk menambahkan Accessories Reject -->
               <form action="{{ route('gudang.acces.reject') }}" method="POST">
                   @csrf

                   <div class="mb-3">
                       <label for="name" class="form-label">Nama Aksesoris</label>
                       <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                       @error('name')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="price" class="form-label">Harga</label>
                       <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                       @error('price')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="capital_price" class="form-label">Harga Modal</label>
                       <input type="number" class="form-control @error('capital_price') is-invalid @enderror" id="capital_price" name="capital_price" value="{{ old('capital_price') }}" required>
                       @error('capital_price')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="code_acces" class="form-label">Kode Aksesoris (Opsional)</label>
                       <input type="text" class="form-control @error('code_acces') is-invalid @enderror" id="code_acces" name="code_acces" value="{{ old('code_acces') }}">
                       @error('code_acces')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="stok" class="form-label">Stok</label>
                       <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok') }}" required>
                       @error('stok')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>
                   <div class="mb-3">
                       <label for="keterangan" class="form-label">Keterangan</label>
                       <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan">{{ old('keterangan') }}</textarea>
                       @error('keterangan')
                       <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <button type="submit" class="btn btn-primary">Simpan</button>
               </form>
           </div>
       </div>
   </div>
@endsection
