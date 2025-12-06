@extends('adminlte::page')

@section('title', 'Data Penerbit')

@section('content_header')
    <h1>Data Penerbit</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Penerbit</h3>
                    <div class="card-tools">
                        <div class="d-flex">
                            {{-- Search --}}
                            <form action="{{ route('buku.index') }}" method="GET" class="mr-2">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari buku dari penerbit..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            @if (Auth::user()->role == 'admin')
                                <a href="{{ route('penerbit.create') }}" class="btn btn-sm btn-primary mr-1" title="Tambah Penerbit">
                                    <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Tambah</span>
                                </a>
                                <a href="{{ route('penerbit.download') }}" target="_blank" class="btn btn-sm btn-info" title="Download PDF">
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

                    {{-- Grid Penerbit --}}
                    <div class="row">
                        @forelse ($penerbit as $item)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="small-box bg-light border shadow-sm h-100">
                                    <div class="inner">
                                        <h4 class="text-truncate" title="{{ $item->nama_penerbit }}">{{ $item->nama_penerbit }}</h4>
                                        <p>{{ $item->buku_count }} Buku</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-building text-black-50"></i>
                                    </div>
                                    
                                    {{-- Footer Aksi --}}
                                    <div class="small-box-footer d-flex p-0 mt-auto bg-white border-top">
                                        <a href="{{ route('buku.index', ['search' => $item->nama_penerbit]) }}" class="flex-fill p-2 text-dark border-right" title="Lihat Koleksi">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        
                                        @if (Auth::user()->role == 'admin')
                                            <a href="{{ route('penerbit.edit', $item) }}" class="flex-fill p-2 text-success border-right" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_penerbit }}')" class="flex-fill p-2 text-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('penerbit.destroy', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-default-warning text-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Data penerbit belum tersedia.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $penerbit->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Penerbit?',
                text: "Anda akan menghapus penerbit: " + name,
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