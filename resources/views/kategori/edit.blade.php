@extends('adminlte::page')

@section('title', 'Edit Kategori')

@section('content_header')
    <h1>Edit Kategori</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Kategori</h3>
                </div>
                
                <form action="{{ route('kategori.update', $kategori->id) }}" method="POST" onsubmit="confirmUpdate(event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="nama_kategori" 
                                class="form-control @error('nama_kategori') is-invalid @enderror" 
                                value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required autofocus>
                            @error('nama_kategori')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('kategori.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-warning float-right">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmUpdate(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Apakah Anda yakin ingin mengubah nama kategori ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107', // Warna warning bootstrap
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            })
        }
    </script>
@stop