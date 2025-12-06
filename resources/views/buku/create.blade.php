@extends('adminlte::page')

@section('title', 'Tambah Buku Baru')

@section('content_header')
    <h1>Tambah Buku Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Detail Buku (Manual)</h3>
                </div>

                <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        {{-- Judul Buku --}}
                        <div class="form-group">
                            <label for="judul_buku">Judul Buku</label>
                            <input type="text" name="judul_buku" id="judul_buku"
                                class="form-control @error('judul_buku') is-invalid @enderror"
                                value="{{ old('judul_buku') }}" required placeholder="Masukkan judul buku">
                            @error('judul_buku')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="form-group">
                            <label for="kategori_id">Kategori</label>
                            <select name="kategori_id" id="kategori_id"
                                class="form-control @error('kategori_id') is-invalid @enderror">
                                <option value="">- Pilih Kategori -</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('kategori_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Penerbit --}}
                        <div class="form-group">
                            <label for="id_penerbit">Penerbit</label>
                            <select name="id_penerbit" id="id_penerbit"
                                class="form-control @error('id_penerbit') is-invalid @enderror" required>
                                <option value="">- Pilih Penerbit -</option>
                                @foreach ($penerbit as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('id_penerbit') == $item->id ? 'selected' : '' }}>{{ $item->nama_penerbit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_penerbit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Penulis --}}
                        <div class="form-group">
                            <label for="id_penulis">Penulis</label>
                            <select name="id_penulis" id="id_penulis" class="form-control select2" required>
                                <option value="">- Pilih Penulis -</option>
                                @foreach ($penulis as $p)
                                    {{-- Sesuaikan variabel old/selected --}}
                                    <option value="{{ $p->id }}">{{ $p->nama_penulis }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tahun Terbit --}}
                        <div class="form-group">
                            <label for="tahun_terbit">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" id="tahun_terbit"
                                class="form-control @error('tahun_terbit') is-invalid @enderror"
                                value="{{ old('tahun_terbit') }}" placeholder="YYYY" required>
                            @error('tahun_terbit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Jumlah Halaman --}}
                        <div class="form-group">
                            <label for="jml_halaman">Jumlah Halaman</label>
                            <input type="number" name="jml_halaman" id="jml_halaman"
                                class="form-control @error('jml_halaman') is-invalid @enderror"
                                value="{{ old('jml_halaman') }}" required>
                            @error('jml_halaman')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Stok --}}
                        <div class="form-group">
                            <label for="stok">Stok Buku</label>
                            <input type="number" name="stok" id="stok"
                                class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', 1) }}"
                                required>
                            @error('stok')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Sampul --}}
                        <div class="form-group">
                            <label for="sampul">Gambar Sampul</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="sampul" id="sampul"
                                        class="custom-file-input @error('sampul') is-invalid @enderror">
                                    <label class="custom-file-label" for="sampul">Pilih file</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Format: jpeg, png, jpg, webp.</small>
                            @error('sampul')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer">
                        <a href="{{ route('buku.index') }}" class="btn btn-default">Batal</a>
                        <button type="submit" class="btn btn-primary float-right">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Tambahkan CSS kustom jika diperlukan --}}
@stop

@section('js')
    <script>
        // Script untuk menampilkan nama file yang dipilih di input file bootstrap
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>
@stop
