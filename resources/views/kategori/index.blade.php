@extends('adminlte::page')

@section('title', 'Kategori Buku')

@section('content_header')
    <h1>Kategori Buku</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kategori</h3>
                    <div class="card-tools">
                        @if (Auth::user()->role == 'admin')
                            <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Tambah Kategori
                            </a>
                        @endif
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

                    {{-- Grid Kategori --}}
                    <div class="row">
                        @forelse ($kategori as $item)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="small-box bg-white border shadow-sm">
                                    <div class="inner">
                                        <h4>{{ $item->nama_kategori }}</h4>
                                        <p>{{ $item->buku_count }} Buku</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tags text-black-50" style="font-size: 50px;"></i>
                                    </div>
                                    
                                    {{-- Footer Aksi --}}
                                    <div class="small-box-footer d-flex bg-light p-0">
                                        <a href="{{ route('buku.index', ['kategori' => $item->nama_kategori]) }}" class="flex-fill p-2 text-dark border-right" title="Lihat Buku">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        
                                        @if (Auth::user()->role == 'admin')
                                            <a href="{{ route('kategori.edit', $item) }}" class="flex-fill p-2 text-success border-right" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_kategori }}')" class="flex-fill p-2 text-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            
                                            {{-- Form Delete Hidden --}}
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('kategori.destroy', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-default-info text-center">
                                    <i class="fas fa-info-circle mr-1"></i> Data kategori belum tersedia.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $kategori->links('pagination::bootstrap-4') }}
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
                title: 'Hapus Kategori?',
                text: "Anda akan menghapus kategori: " + name,
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