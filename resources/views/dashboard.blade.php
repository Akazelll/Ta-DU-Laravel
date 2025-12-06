@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- Greeting Section --}}
    <div class="row">
        <div class="col-12">
            <div class="callout callout-info">
                <h5>{{ $greeting }}, {{ Auth::user()->name }}!</h5>
                <p>
                    @if (Auth::user()->role == 'admin')
                        Berikut adalah ringkasan dan analitik dari aplikasi perpustakaan Anda.
                    @else
                        Selamat datang di DigiPustaka. Lacak aktivitas peminjaman dan temukan buku favoritmu!
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if (Auth::user()->role == 'admin')
        {{-- ================= TAMPILAN ADMIN ================= --}}
        
        {{-- Small Boxes (Statistik Utama) --}}
        <div class="row">
            {{-- Total Buku --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalBuku }}</h3>
                        <p>Total Buku</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <a href="{{ route('buku.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            {{-- Total Penerbit --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalPenerbit }}</h3>
                        <p>Total Penerbit</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <a href="{{ route('penerbit.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            {{-- Peminjaman Aktif --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $peminjamanAktif }}</h3>
                        <p>Peminjaman Aktif</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <a href="{{ route('peminjaman.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            {{-- Total Pengguna --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $totalUser }}</h3>
                        <p>Total Pengguna</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Tren Peminjaman (Chart) --}}
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Tren Peminjaman (6 Bulan Terakhir)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="loanChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lists (Buku Populer & Anggota Aktif) --}}
            <div class="col-md-4">
                {{-- Buku Terpopuler --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buku Terpopuler</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            @forelse ($bukuPopuler as $buku)
                                <li class="item">
                                    <div class="product-info ml-0">
                                        <a href="{{ route('buku.show', $buku) }}" class="product-title">{{ $buku->judul_buku }}
                                            <span class="badge badge-info float-right">{{ $buku->peminjaman_count }}x</span>
                                        </a>
                                        <span class="product-description">
                                            {{ $buku->penerbit->nama_penerbit }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="item"><span class="text-muted p-3">Belum ada data.</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Anggota Teraktif --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Anggota Teraktif</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="users-list clearfix">
                            @forelse ($anggotaAktif as $user)
                                <li style="width: 100%; text-align: left; padding: 10px; border-bottom: 1px solid #f4f4f4;">
                                    <span class="users-list-name" style="font-weight: bold;">{{ $user->name }}</span>
                                    <span class="users-list-date">{{ $user->email }}</span>
                                    <span class="float-right badge badge-success">{{ $user->peminjaman_count }} Peminjaman</span>
                                </li>
                            @empty
                                <p class="text-center text-muted p-3">Belum ada data.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- ================= TAMPILAN USER BIASA ================= --}}
        
        <div class="row">
            {{-- Kolom Kiri: Peminjaman & Buku Populer --}}
            <div class="col-lg-9">
                {{-- Sedang Dipinjam --}}
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-book-reader mr-1"></i> Buku yang Sedang Anda Pinjam</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($sedangDipinjam as $peminjaman)
                            <div class="callout {{ $peminjaman->is_overdue ? 'callout-danger' : 'callout-warning' }}">
                                <h5>{{ $peminjaman->buku?->judul_buku ?? 'Buku Telah Dihapus' }}</h5>
                                <p>
                                    Batas Waktu: <strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_harus_kembali)->isoFormat('D MMMM Y') }}</strong>
                                    @if ($peminjaman->is_overdue)
                                        <br><span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Sudah Terlambat!</span>
                                    @endif
                                </p>
                            </div>
                        @empty
                            <p class="text-muted">Anda sedang tidak meminjam buku saat ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Buku Populer Grid --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buku Paling Populer</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($bukuPopuler as $buku)
                                <div class="col-sm-4 col-md-3 col-lg-2 mb-4">
                                    <a href="{{ route('buku.show', $buku) }}" class="text-decoration-none text-dark">
                                        <div class="card h-100 shadow-sm">
                                            @if ($buku->sampul)
                                                <img src="{{ asset('storage/' . $buku->sampul) }}" class="card-img-top" alt="{{ $buku->judul_buku }}" style="height: 150px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex justify-content-center align-items-center" style="height: 150px;">
                                                    <i class="fas fa-book fa-3x text-secondary"></i>
                                                </div>
                                            @endif
                                            <div class="card-body p-2">
                                                <h6 class="card-title text-truncate w-100" style="font-size: 0.9rem;">{{ $buku->judul_buku }}</h6>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Statistik User --}}
            <div class="col-lg-3">
                <div class="info-box mb-3 bg-info">
                    <span class="info-box-icon"><i class="fas fa-book-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Dibaca</span>
                        <span class="info-box-number">{{ $totalDibaca }}</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-danger">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Denda</span>
                        <span class="info-box-number">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('profile.history') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-history mr-1"></i> Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
    @if (Auth::user()->role == 'admin')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('loanChart').getContext('2d');
                
                // Data dari controller
                const labels = {!! json_encode($loanChartLabels) !!};
                const data = {!! json_encode($loanChartData) !!};

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: data,
                            backgroundColor: 'rgba(60, 141, 188, 0.9)',
                            borderColor: 'rgba(60, 141, 188, 0.8)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>
    @endif
@stop