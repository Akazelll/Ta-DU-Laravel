@extends('adminlte::page')

@section('title', 'Tambah Penulis')

@section('content_header')
    <h1>Tambah Penulis Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Penulis</h3>
                </div>
                
                <form action="{{ route('penulis.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- Nama Penulis --}}
                        <div class="form-group">
                            <label for="nama_penulis">Nama Penulis <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penulis" id="nama_penulis" 
                                class="form-control @error('nama_penulis') is-invalid @enderror" 
                                value="{{ old('nama_penulis') }}" placeholder="Contoh: Tere Liye" required autofocus>
                            @error('nama_penulis')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Bio --}}
                        <div class="form-group">
                            <label for="bio">Biografi Singkat (Opsional)</label>
                            <textarea name="bio" id="bio" rows="4" 
                                class="form-control @error('bio') is-invalid @enderror" 
                                placeholder="Tuliskan sedikit tentang penulis ini...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('penulis.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop