@extends('adminlte::page')

@section('title', 'Edit Penerbit')

@section('content_header')
    <h1>Edit Data Penerbit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Penerbit</h3>
                </div>
                
                <form action="{{ route('penerbit.update', $penerbit->id) }}" method="POST" onsubmit="confirmUpdate(event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_penerbit">Nama Penerbit</label>
                            <input type="text" name="nama_penerbit" id="nama_penerbit" 
                                class="form-control @error('nama_penerbit') is-invalid @enderror" 
                                value="{{ old('nama_penerbit', $penerbit->nama_penerbit) }}" required autofocus>
                            @error('nama_penerbit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('penerbit.index') }}" class="btn btn-default">Batal</a>
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
                text: "Apakah Anda yakin ingin mengubah nama penerbit ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
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