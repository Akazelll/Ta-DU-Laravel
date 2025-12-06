@extends('adminlte::page')

@section('title', 'Tambah Penerbit')

@section('content_header')
    <h1>Tambah Penerbit Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Penerbit</h3>
                </div>
                
                <form action="{{ route('penerbit.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_penerbit">Nama Penerbit</label>
                            <input type="text" name="nama_penerbit" id="nama_penerbit" 
                                class="form-control @error('nama_penerbit') is-invalid @enderror" 
                                value="{{ old('nama_penerbit') }}" placeholder="Masukkan nama penerbit" required autofocus>
                            @error('nama_penerbit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('penerbit.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop