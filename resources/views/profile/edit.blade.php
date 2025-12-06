@extends('adminlte::page')

@section('title', 'Profil Saya')

@section('content_header')
    <h1>Profil Saya</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            {{-- Update Profile --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi Profil</h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Ganti Password</h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="col-md-6">
             {{-- Delete User --}}
             <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Hapus Akun</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-light text-danger border-danger">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                        Tindakan ini tidak dapat dibatalkan. Semua data dan riwayat peminjaman Anda akan dihapus secara permanen.
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@stop