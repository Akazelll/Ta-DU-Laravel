@extends('adminlte::page')

@section('title', 'Transaksi Peminjaman')

@section('content_header')
    <h1>Transaksi Peminjaman</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Peminjaman</h3>
                    <div class="card-tools">
                        <div class="d-flex">
                            {{-- Search --}}
                            <form action="{{ route('peminjaman.index') }}" method="GET" class="mr-2">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari peminjam/buku..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            {{-- Tombol Tambah --}}
                            @if (Auth::user()->role == 'admin')
                                <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary" title="Buat Peminjaman Baru">
                                    <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Peminjaman Baru</span>
                                </a>
                                <a href="{{ route('laporan.peminjaman.cetak') }}" target="_blank" class="btn btn-sm btn-info ml-1" title="Cetak Laporan">
                                    <i class="fas fa-print"></i> <span class="d-none d-md-inline">Cetak</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjaman as $key => $item)
                                <tr>
                                    <td>{{ $peminjaman->firstItem() + $key }}</td>
                                    <td>
                                        <div class="user-block">
                                            <span class="username ml-0" style="font-size: 1rem;">
                                                <a href="#">{{ $item->user->name ?? 'User Dihapus' }}</a>
                                            </span>
                                            <span class="description ml-0">ID: {{ $item->user->kode_anggota ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $item->buku->judul_buku ?? 'Buku Dihapus' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->tgl_harus_kembali)->format('d/m/Y') }}
                                        @if($item->is_overdue && $item->status == 'pinjam')
                                            <i class="fas fa-exclamation-circle text-danger" title="Terlambat!"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'pinjam')
                                            <span class="badge badge-warning">Dipinjam</span>
                                            @if($item->is_overdue)
                                                <br><small class="text-danger font-weight-bold">Terlambat</small>
                                            @endif
                                        @elseif ($item->status == 'kembali')
                                            <span class="badge badge-success">Dikembalikan</span>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') }}</small>
                                        @else
                                            <span class="badge badge-secondary">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->denda_terhitung > 0)
                                            <span class="text-danger font-weight-bold">Rp {{ number_format($item->denda_terhitung, 0, ',', '.') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status == 'pinjam' && Auth::user()->role == 'admin')
                                            <div class="btn-group">
                                                {{-- Tombol Kembalikan --}}
                                                <button type="button" onclick="confirmReturn({{ $item->id }}, '{{ $item->user->name }}', '{{ $item->buku->judul_buku }}')" class="btn btn-sm btn-success" title="Kembalikan Buku">
                                                    <i class="fas fa-check"></i> Kembalikan
                                                </button>
                                                
                                                {{-- Tombol Edit (Jika Perlu) --}}
                                                <a href="{{ route('peminjaman.edit', $item) }}" class="btn btn-sm btn-default" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>

                                            {{-- Form Pengembalian Hidden --}}
                                            <form id="return-form-{{ $item->id }}" action="{{ route('peminjaman.update', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="kembali">
                                            </form>

                                        @elseif ($item->status == 'kembali')
                                            <button class="btn btn-sm btn-outline-secondary" disabled>Selesai</button>
                                        @endif
                                        
                                        @if ($item->denda_terhitung > 0 && $item->status_denda != 'lunas' && Auth::user()->role == 'admin')
                                            <button type="button" class="btn btn-sm btn-danger ml-1" onclick="bayarDenda({{ $item->id }})">
                                                <i class="fas fa-money-bill"></i> Bayar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $peminjaman->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi Pengembalian
        function confirmReturn(id, userName, bookTitle) {
            Swal.fire({
                title: 'Konfirmasi Pengembalian',
                html: `Buku: <b>${bookTitle}</b><br>Peminjam: <b>${userName}</b><br><br>Apakah buku sudah diterima kembali?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terima Buku!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('return-form-' + id).submit();
                }
            })
        }

        // Pesan Sukses
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        // Pesan Error
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@stop