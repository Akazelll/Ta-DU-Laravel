@extends('adminlte::page')

@section('title', 'Buat Peminjaman')

@section('content_header')
    <h1>Buat Peminjaman Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Transaksi</h3>
                </div>
                
                <form action="{{ route('peminjaman.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- Pilih Anggota --}}
                        <div class="form-group">
                            <label>Anggota Peminjam</label>
                            <select name="id_user" class="form-control select2" style="width: 100%;" required>
                                <option value="" selected disabled>- Cari Anggota (Nama/Email) -</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->kode_anggota ?? 'No ID' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_user')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Pilih Buku --}}
                        <div class="form-group">
                            <label>Buku yang Dipinjam</label>
                            <select name="id_buku" class="form-control select2" style="width: 100%;" required>
                                <option value="" selected disabled>- Cari Buku (Judul/ISBN) -</option>
                                @foreach ($buku as $item)
                                    <option value="{{ $item->id }}" {{ old('id_buku') == $item->id ? 'selected' : '' }} {{ $item->stok <= 0 ? 'disabled' : '' }}>
                                        {{ $item->judul_buku }} (Stok: {{ $item->stok }}) {{ $item->stok <= 0 ? '- HABIS' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_buku')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Tanggal Pinjam --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Pinjam</label>
                                    <input type="date" name="tgl_pinjam" class="form-control" 
                                        value="{{ old('tgl_pinjam', date('Y-m-d')) }}" required>
                                    @error('tgl_pinjam')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- Tanggal Kembali --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Harus Kembali</label>
                                    {{-- Default 7 hari ke depan --}}
                                    <input type="date" name="tgl_harus_kembali" class="form-control" 
                                        value="{{ old('tgl_harus_kembali', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                    @error('tgl_harus_kembali')
                                        <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Keterangan Opsional --}}
                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Kondisi buku, catatan khusus, dll...">{{ old('keterangan') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-primary float-right">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- Info Panel Kanan --}}
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h5 class="card-title">Informasi</h5>
                </div>
                <div class="card-body">
                    <p>Pastikan anggota tidak memiliki tanggungan denda atau buku yang belum dikembalikan melebihi batas maksimal.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-calendar-week text-primary"></i> Durasi default: 7 Hari</li>
                        <li><i class="fas fa-exclamation-triangle text-warning"></i> Denda keterlambatan berlaku</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Mengaktifkan Select2 CSS dari CDN jika belum ada di config adminlte --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet">
    <style>
        /* Perbaikan style select2 agar sesuai tinggi input Bootstrap 4 */
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.25rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.25rem !important;
        }
    </style>
@stop

@section('js')
    {{-- Mengaktifkan Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 pada class .select2
            $('.select2').select2({
                theme: 'bootstrap4', // Menggunakan tema bootstrap 4 agar menyatu dengan AdminLTE
                placeholder: 'Pilih opsi',
                allowClear: true
            });
        });
    </script>
@stop