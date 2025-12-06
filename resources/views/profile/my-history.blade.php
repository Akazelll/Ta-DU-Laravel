@extends('adminlte::page')

@section('title', 'Riwayat Peminjaman Saya')

@section('content_header')
    <h1>Riwayat Peminjaman Saya</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i> Aktivitas Peminjaman Anda
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Jika tidak ada data --}}
                    @if($peminjaman->isEmpty())
                        <div class="alert alert-default-info text-center">
                            <i class="fas fa-info-circle mr-1"></i> Anda belum memiliki riwayat peminjaman.
                        </div>
                    @else
                        {{-- Timeline --}}
                        <div class="timeline">
                            {{-- Loop data peminjaman --}}
                            @foreach($peminjaman as $item)
                                {{-- Label Tanggal --}}
                                <div class="time-label">
                                    <span class="bg-{{ $item->status == 'pinjam' ? 'warning' : 'success' }}">
                                        {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                    </span>
                                </div>
                                
                                {{-- Item Timeline --}}
                                <div>
                                    {{-- Ikon berdasarkan status --}}
                                    @if ($item->is_overdue && $item->status == 'pinjam')
                                        <i class="fas fa-exclamation-triangle bg-danger"></i>
                                    @elseif($item->status == 'pinjam')
                                        <i class="fas fa-book-reader bg-warning"></i>
                                    @else
                                        <i class="fas fa-check bg-success"></i>
                                    @endif

                                    <div class="timeline-item">
                                        {{-- Waktu (Jam) --}}
                                        <span class="time"><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('H:i') }}</span>

                                        {{-- Header --}}
                                        <h3 class="timeline-header">
                                            @if($item->status == 'pinjam')
                                                Anda <strong>meminjam</strong> buku
                                            @else
                                                Anda <strong>mengembalikan</strong> buku
                                            @endif
                                            <a href="{{ $item->buku ? route('buku.show', $item->buku) : '#' }}">{{ $item->buku->judul_buku ?? '(Buku Dihapus)' }}</a>
                                        </h3>

                                        {{-- Body (Detail) --}}
                                        <div class="timeline-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <strong><i class="fas fa-calendar-alt mr-1"></i> Batas Kembali:</strong><br>
                                                    <span class="{{ $item->is_overdue && $item->status == 'pinjam' ? 'text-danger font-weight-bold' : '' }}">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_harus_kembali)->format('d F Y') }}
                                                    </span>
                                                </div>
                                                <div class="col-sm-4">
                                                    <strong><i class="fas fa-info-circle mr-1"></i> Status:</strong><br>
                                                    @if ($item->is_overdue && $item->status == 'pinjam')
                                                        <span class="badge badge-danger">Terlambat</span>
                                                    @elseif($item->status == 'pinjam')
                                                        <span class="badge badge-warning">Sedang Dipinjam</span>
                                                    @else
                                                        <span class="badge badge-success">Dikembalikan</span>
                                                        <small class="text-muted">({{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }})</small>
                                                    @endif
                                                </div>
                                                <div class="col-sm-4">
                                                    <strong><i class="fas fa-coins mr-1"></i> Denda:</strong><br>
                                                    @if($item->denda_terhitung > 0)
                                                        <span class="text-danger">Rp {{ number_format($item->denda_terhitung, 0, ',', '.') }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Icon Akhir Timeline --}}
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $peminjaman->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Sedikit penyesuaian agar timeline item terlihat rapi */
        .timeline-header a {
            color: #007bff;
            font-weight: 600;
        }
        .timeline-header a:hover {
            text-decoration: underline;
        }
    </style>
@stop