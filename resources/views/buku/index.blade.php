@extends('adminlte::page')

@section('title', 'Koleksi Buku')

@section('content_header')
    <h1>Koleksi Buku</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pustaka</h3>

                    <div class="card-tools">
                        <div class="d-flex">
                            {{-- Form Pencarian --}}
                            <form action="{{ route('buku.index') }}" method="GET" class="mr-2">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari buku/penerbit..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- Tombol Aksi Admin --}}
                            @if (Auth::user()->role == 'admin')
                                <a href="{{ route('buku.create') }}" class="btn btn-sm btn-primary mr-1" title="Tambah Buku">
                                    <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Tambah</span>
                                </a>
                                <a href="{{ route('buku.download') }}" target="_blank" class="btn btn-sm btn-info" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">Unduh</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Alert Success --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Grid Buku --}}
                    <div class="row">
                        @forelse ($buku as $item)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                                <div class="card h-100 shadow-sm border">
                                    {{-- Gambar Sampul dengan Badge Stok --}}
                                    <div class="position-relative">
                                        <a href="{{ route('buku.show', $item) }}">
                                            @if ($item->sampul)
                                                <img src="{{ asset('storage/' . $item->sampul) }}" class="card-img-top" alt="{{ $item->judul_buku }}" style="height: 220px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex justify-content-center align-items-center text-secondary" style="height: 220px;">
                                                    <div class="text-center">
                                                        <i class="fas fa-book fa-3x mb-2"></i>
                                                        <br><small>No Cover</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="position-absolute top-0 right-0 bg-{{ $item->stok > 0 ? 'success' : 'danger' }} px-2 py-1 text-white shadow-sm" style="font-size: 0.75rem; border-bottom-left-radius: 5px; opacity: 0.9;">
                                            Stok: {{ $item->stok }}
                                        </div>
                                    </div>

                                    <div class="card-body p-3 d-flex flex-column">
                                        {{-- Judul --}}
                                        <h6 class="font-weight-bold mb-1" style="line-height: 1.4;">
                                            <a href="{{ route('buku.show', $item) }}" class="text-dark text-decoration-none text-truncate d-block" title="{{ $item->judul_buku }}">
                                                {{ $item->judul_buku }}
                                            </a>
                                        </h6>
                                        
                                        {{-- Info Penerbit --}}
                                        <small class="text-muted text-truncate mb-2" title="{{ $item->penerbit->nama_penerbit }}">
                                            <i class="fas fa-building mr-1"></i> {{ $item->penerbit->nama_penerbit }}
                                        </small>

                                        {{-- Detail Tahun & Halaman --}}
                                        <div class="d-flex justify-content-between small text-secondary mt-auto">
                                            <span>{{ $item->tahun_terbit }}</span>
                                            <span>{{ $item->jml_halaman }} Hal</span>
                                        </div>

                                        {{-- Tombol Edit/Hapus (Admin Only) --}}
                                        @if (Auth::user()->role == 'admin')
                                            <div class="mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between gap-1">
                                                    <a href="{{ route('buku.edit', $item) }}" class="btn btn-xs btn-outline-success flex-fill mr-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->judul_buku }}')" class="btn btn-xs btn-outline-danger flex-fill" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <form id="delete-form-{{ $item->id }}" action="{{ route('buku.destroy', $item->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-default-warning text-center">
                                    <i class="fas fa-search fa-2x mb-3 d-block"></i>
                                    Tidak ada buku yang ditemukan. Coba kata kunci lain.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $buku->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, title) {
            Swal.fire({
                title: 'Hapus Buku?',
                text: "Anda akan menghapus: " + title,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@stop

@section('css')
    <style>
        .card-img-top {
            transition: transform 0.3s;
        }
        .card:hover .card-img-top {
            transform: scale(1.05);
        }
        .position-relative {
            overflow: hidden; /* Agar zoom effect tidak keluar border */
        }
    </style>
@stop