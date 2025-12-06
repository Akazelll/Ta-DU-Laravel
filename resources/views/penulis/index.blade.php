@extends('adminlte::page')

@section('title', 'Data Penulis')

@section('content_header')
    <h1>Data Penulis</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Penulis Buku</h3>
                    <div class="card-tools">
                        <div class="d-flex">
                            {{-- Search --}}
                            <form action="{{ route('penulis.index') }}" method="GET" class="mr-2">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama penulis..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            {{-- Tombol Tambah --}}
                            <a href="{{ route('penulis.create') }}" class="btn btn-sm btn-primary" title="Tambah Penulis">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Nama Penulis</th>
                                <th>Biografi Singkat</th>
                                <th class="text-center" style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penulis as $key => $item)
                                <tr>
                                    <td>{{ $penulis->firstItem() + $key }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ $item->nama_penulis }}</span>
                                    </td>
                                    <td>
                                        {{ Str::limit($item->bio, 50) ?: '-' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('penulis.edit', $item->id) }}" class="btn btn-xs btn-warning mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_penulis }}')" class="btn btn-xs btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('penulis.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data penulis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $penulis->links('pagination::bootstrap-4') }}
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
                title: 'Hapus Penulis?',
                text: "Anda akan menghapus penulis: " + name,
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

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@stop