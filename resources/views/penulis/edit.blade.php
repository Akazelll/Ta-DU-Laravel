@extends('adminlte::page')

@section('title', 'Edit Penulis')

@section('content_header')
    <h1>Edit Data Penulis</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Penulis</h3>
                </div>
                
                <form action="{{ route('penulis.update', $penulis->id) }}" method="POST" onsubmit="confirmUpdate(event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Nama Penulis --}}
                        <div class="form-group">
                            <label for="nama_penulis">Nama Penulis <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penulis" id="nama_penulis" 
                                class="form-control @error('nama_penulis') is-invalid @enderror" 
                                value="{{ old('nama_penulis', $penulis->nama_penulis) }}" required>
                            @error('nama_penulis')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Bio --}}
                        <div class="form-group">
                            <label for="bio">Biografi Singkat</label>
                            <textarea name="bio" id="bio" rows="4" 
                                class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $penulis->bio) }}</textarea>
                            @error('bio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('penulis.index') }}" class="btn btn-default">Batal</a>
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
                text: "Pastikan data penulis sudah benar.",
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