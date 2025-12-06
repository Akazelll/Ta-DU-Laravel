@extends('adminlte::page')

@section('title', 'Edit Data Buku')

@section('content_header')
    <h1>Edit Data Buku</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Buku</h3>
                </div>

                {{-- Menambahkan onsubmit untuk memanggil SweetAlert --}}
                <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data"
                    onsubmit="confirmUpdate(event)">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Judul Buku --}}
                        <div class="form-group">
                            <label for="judul_buku">Judul Buku</label>
                            <input type="text" name="judul_buku" id="judul_buku"
                                class="form-control @error('judul_buku') is-invalid @enderror"
                                value="{{ old('judul_buku', $buku->judul_buku) }}" required>
                            @error('judul_buku')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sinopsis --}}
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea name="sinopsis" id="sinopsis" rows="4" class="form-control @error('sinopsis') is-invalid @enderror">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                            @error('sinopsis')
                                <span class="invalid-feedback">{{ $message }}</span>
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
                                        {{ old('id_penerbit', $buku->id_penerbit) == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_penerbit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_penerbit')
                                <span class="invalid-feedback">{{ $message }}</span>
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

                        <div class="row">
                            {{-- Tahun Terbit --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tahun_terbit">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" id="tahun_terbit"
                                        class="form-control @error('tahun_terbit') is-invalid @enderror"
                                        value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" placeholder="YYYY" required>
                                    @error('tahun_terbit')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Jumlah Halaman --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jml_halaman">Jumlah Halaman</label>
                                    <input type="number" name="jml_halaman" id="jml_halaman"
                                        class="form-control @error('jml_halaman') is-invalid @enderror"
                                        value="{{ old('jml_halaman', $buku->jml_halaman) }}" required>
                                    @error('jml_halaman')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Stok --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stok">Stok Buku</label>
                                    <input type="number" name="stok" id="stok"
                                        class="form-control @error('stok') is-invalid @enderror"
                                        value="{{ old('stok', $buku->stok ?? 0) }}" required>
                                    @error('stok')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Sampul --}}
                        <div class="form-group">
                            <label for="sampul">Ganti Gambar Sampul (Opsional)</label>

                            @if ($buku->sampul)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $buku->sampul) }}" alt="Sampul saat ini"
                                        class="img-thumbnail" style="max-width: 150px;">
                                    <p class="text-muted text-sm">Sampul saat ini</p>
                                </div>
                            @endif

                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="sampul" id="sampul"
                                        class="custom-file-input @error('sampul') is-invalid @enderror">
                                    <label class="custom-file-label" for="sampul">Pilih file baru...</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti sampul.</small>
                            @error('sampul')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer">
                        <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success float-right">Update Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Script untuk menampilkan nama file
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        function confirmUpdate(event) {
            event.preventDefault(); // Mencegah form submit langsung

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Apakah Anda yakin ingin menyimpan perubahan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745', // Warna success Bootstrap
                cancelButtonColor: '#6c757d', // Warna secondary Bootstrap
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            })
        }
    </script>
@stop
