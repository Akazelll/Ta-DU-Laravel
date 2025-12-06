@extends('adminlte::page')

@section('title', 'Edit Anggota')

@section('content_header')
    <h1>Edit Data Anggota</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Form Edit User</h3>
                </div>
                
                <form action="{{ route('users.update', $user->id) }}" method="POST" onsubmit="confirmUpdate(event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Nama --}}
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" name="name" id="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Kode Anggota (Read Only biasanya, tapi bisa diedit jika perlu) --}}
                        <div class="form-group">
                            <label for="kode_anggota">Kode Anggota</label>
                            <input type="text" name="kode_anggota" id="kode_anggota" 
                                class="form-control @error('kode_anggota') is-invalid @enderror" 
                                value="{{ old('kode_anggota', $user->kode_anggota) }}">
                            <small class="text-muted">Kosongkan jika ingin digenerate otomatis (jika didukung sistem).</small>
                            @error('kode_anggota')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Role (Jika admin bisa ubah role) --}}
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User Biasa</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('users.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-warning float-right">Update Data</button>
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
                text: "Pastikan data yang diinput sudah benar.",
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