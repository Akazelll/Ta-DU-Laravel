@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Data Peminjaman Buku</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Peminjaman</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            {{-- Alert HTML manual dihapus agar tidak muncul double (karena sudah ada SweetAlert) --}}

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table mr-1"></i> Daftar Transaksi
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Pinjam Baru
                        </a>
                        <a href="{{ route('laporan.peminjaman.cetak') }}" class="btn btn-danger btn-sm ml-1" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-bordered text-nowrap table-striped">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th style="width: 5%">No</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th>Status Buku</th>
                                <th>Status Denda</th>
                                <th style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $key => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $peminjaman->firstItem() + $key }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->user->name ?? 'User dihapus' }}</strong><br>
                                    <small class="text-muted">Kode: {{ $item->user->kode_anggota ?? '-' }}</small>
                                </td>
                                <td class="align-middle">
                                    {{ $item->buku->judul_buku ?? 'Buku dihapus' }}
                                </td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y') }}</td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->tanggal_harus_kembali)->format('d/m/Y') }}</td>
                                <td class="text-center align-middle">
                                    @if($item->status == 'pinjam')
                                        @if(\Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($item->tanggal_harus_kembali)->startOfDay()))
                                            <span class="badge badge-danger">Terlambat</span>
                                        @else
                                            <span class="badge badge-warning">Dipinjam</span>
                                        @endif
                                    @else
                                        <span class="badge badge-success">Dikembalikan</span>
                                        <div class="small text-muted">{{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex flex-column" style="font-size: 0.9rem;">
                                        <span>Total: Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                        
                                        @if($item->sisa_denda > 0)
                                            <span class="text-danger font-weight-bold">
                                                Sisa: Rp {{ number_format($item->sisa_denda, 0, ',', '.') }}
                                            </span>
                                        @elseif($item->denda > 0)
                                            <span class="badge badge-success mt-1">Lunas</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    {{-- Tombol Kembalikan --}}
                                    @if($item->status == 'pinjam')
                                        {{-- Update: Hapus onsubmit, tambahkan class form-kembali --}}
                                        <form action="{{ route('peminjaman.update', $item->id) }}" method="POST" class="d-inline form-kembali">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="kembali">
                                            {{-- Update: Button type jadi button (bukan submit) dan tambah class btn-action-kembali --}}
                                            <button type="button" class="btn btn-success btn-sm btn-action-kembali" title="Kembalikan Buku">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tombol Bayar Denda --}}
                                    @if($item->sisa_denda > 0)
                                        <button type="button" class="btn btn-danger btn-sm ml-1" 
                                            data-toggle="modal" 
                                            data-target="#modalBayar{{ $item->id }}"
                                            title="Bayar Denda">
                                            <i class="fas fa-money-bill-wave"></i> Bayar
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- MODAL BAYAR DENDA (Per Item) --}}
                            @if($item->sisa_denda > 0)
                            <div class="modal fade" id="modalBayar{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">
                                                <i class="fas fa-cash-register mr-1"></i> Pembayaran Denda
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('peminjaman.bayarDenda', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body text-left">
                                                <div class="callout callout-danger">
                                                    <h5>{{ $item->user->name }}</h5>
                                                    <p>Sedang membayar denda untuk buku: <strong>{{ $item->buku->judul_buku }}</strong></p>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="info-box bg-light shadow-none border">
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Total Denda</span>
                                                                <span class="info-box-number">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="info-box bg-warning shadow-none border">
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Sisa Tagihan</span>
                                                                <span class="info-box-number">Rp {{ number_format($item->sisa_denda, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group mt-2">
                                                    <label>Nominal Pembayaran</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" 
                                                               name="jumlah_bayar" 
                                                               class="form-control" 
                                                               placeholder="Contoh: 5000" 
                                                               min="1" 
                                                               max="{{ $item->sisa_denda }}" 
                                                               required>
                                                    </div>
                                                    <small class="text-muted">Maksimal: Rp {{ number_format($item->sisa_denda, 0, ',', '.') }}</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {{-- END MODAL --}}

                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $peminjaman->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .align-middle { vertical-align: middle !important; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                html: '<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            });
        @endif


        $(document).ready(function() {
            $('.btn-action-kembali').click(function(e) {
                e.preventDefault(); 

                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Konfirmasi Pengembalian',
                    text: "Pastikan fisik buku sudah diterima dan dicek kondisinya.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kembalikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); 
                    }
                });
            });
        });
    </script>
@stop