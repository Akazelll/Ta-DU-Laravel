@extends('adminlte::page')

{{-- Konfigurasi Judul Halaman --}}
@section('title')
    {{ config('app.name', 'Laravel') }}
    @hasSection('title')
        | @yield('title')
    @endif
@stop

{{-- Header Halaman (Misal: Judul Besar di atas konten) --}}
@section('content_header')
    @hasSection('content_header')
        @yield('content_header')
    @endif
@stop

{{-- Konten Utama --}}
@section('content')
    @yield('content')
@stop

{{-- Footer Halaman --}}
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Perpustakaan Digital</a>.</strong> All rights reserved.
@stop

{{-- CSS Tambahan (Global) --}}
@section('css')
    {{-- Tambahkan custom css jika perlu --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

{{-- JS Tambahan (Global) --}}
@section('js')
    <script> console.log('AdminLTE loaded!'); </script>
@stop