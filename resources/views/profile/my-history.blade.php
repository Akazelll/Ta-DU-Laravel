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
                                {{-- Setup Variabel Helper untuk Logic View --}}
                                @php
                                    $tgl_kembali = $item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali) : null;
                                    $jatuh_tempo = \Carbon\Carbon::parse($item->tanggal_harus_kembali);
                                    $is_overdue = !$tgl_kembali && now()->gt($jatuh_tempo);
                                    $bg_class = $item->status == 'pinjam' ? ($is_overdue ? 'danger' : 'warning') : 'success';
                                @endphp

                                {{-- Label Tanggal --}}
                                <div class="time-label">
                                    <span class="bg-{{ $bg_class }}">
                                        {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                    </span>
                                </div>
                                
                                {{-- Item Timeline --}}
                                <div>
                                    {{-- Ikon berdasarkan status --}}
                                    @if ($item->status == 'pinjam')
                                        @if($is_overdue)
                                            <i class="fas fa-exclamation-triangle bg-danger"></i>
                                        @else
                                            <i class="fas fa-book-reader bg-warning"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-check bg-success"></i>
                                    @endif

                                    <div class="timeline-item">
                                        {{-- Waktu (Jam) --}}
                                        <span class="time"><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('H:i') }}</span>

                                        {{-- Header --}}
                                        <h3 class="timeline-header">
                                            @if($item->status == 'pinjam')
                                                Anda sedang <strong>meminjam</strong> buku
                                            @else
                                                Anda telah <strong>mengembalikan</strong> buku
                                            @endif
                                            <a href="{{ $item->buku ? route('buku.show', $item->buku->id) : '#' }}">
                                                {{ $item->buku->judul_buku ?? '(Buku Telah Dihapus)' }}
                                            </a>
                                        </h3>

                                        {{-- Body (Detail) --}}
                                        <div class="timeline-body">
                                            <div class="row">
                                                <div class="col-sm-4 border-right">
                                                    <strong><i class="fas fa-calendar-alt mr-1"></i> Batas Kembali:</strong><br>
                                                    <span class="{{ $is_overdue ? 'text-danger font-weight-bold' : '' }}">
                                                        {{ $jatuh_tempo->format('d F Y') }}
                                                    </span>
                                                    @if($is_overdue)
                                                        <br><small class="text-danger">(Terlambat {{ $jatuh_tempo->diffInDays(now()) }} hari)</small>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-sm-4 border-right">
                                                    <strong><i class="fas fa-info-circle mr-1"></i> Status Buku:</strong><br>
                                                    @if($item->status == 'pinjam')
                                                        @if($is_overdue)
                                                            <span class="badge badge-danger">Terlambat</span>
                                                        @else
                                                            <span class="badge badge-warning">Sedang Dipinjam</span>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-success">Dikembalikan</span>
                                                        <div class="small text-muted mt-1">
                                                            Tgl Kembali: {{ $tgl_kembali->format('d/m/Y') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-sm-4">
                                                    <strong><i class="fas fa-coins mr-1"></i> Info Denda:</strong><br>
                                                    @if($item->denda > 0)
                                                        <span class="text-danger font-weight-bold">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                                        
                                                        <div class="mt-1">
                                                            @if($item->status_denda == 'Lunas')
                                                                <span class="badge badge-success">Lunas</span>
                                                            @else
                                                                <span class="badge badge-danger">Belum Lunas</span>
                                                                @if($item->denda_dibayar > 0)
                                                                    <small class="d-block text-muted">Dibayar: {{ number_format($item->denda_dibayar) }}</small>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-success">Tidak ada denda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Footer (Optional actions) --}}
                                        @if($item->status == 'pinjam')
                                        <div class="timeline-footer">
                                            <a href="{{ route('buku.show', $item->id_buku) }}" class="btn btn-primary btn-sm">Lihat Buku</a>
                                        </div>
                                        @endif
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
        .timeline-header a {
            color: #007bff;
            font-weight: 600;
        }
        .timeline-header a:hover {
            text-decoration: underline;
        }
        .badge {
            font-size: 90%;
        }
    </style>
@stop