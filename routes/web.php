<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenerbitController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenulisController; // Pastikan ini ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $hour = now('Asia/Jakarta')->hour;
    if ($hour < 11) {
        $greeting = 'Selamat Pagi 🌄';
    } elseif ($hour < 15) {
        $greeting = 'Selamat Siang 🌞';
    } elseif ($hour < 19) {
        $greeting = 'Selamat Sore 🌤️';
    } else {
        $greeting = 'Selamat Malam 🌙';
    }

    $viewData = ['greeting' => $greeting];

    if (Auth::user()->role == 'admin') {
        $viewData['totalPenerbit'] = \App\Models\Penerbit::count();
        $viewData['totalBuku'] = \App\Models\Buku::count();
        $viewData['totalUser'] = \App\Models\User::count();
        $viewData['peminjamanAktif'] = \App\Models\Peminjaman::where('status', 'pinjam')->count();
        $viewData['bukuPopuler'] = \App\Models\Buku::withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(5)->get();
        $viewData['anggotaAktif'] = \App\Models\User::withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(5)->get();

        $loanStats = \App\Models\Peminjaman::select(DB::raw('YEAR(tgl_pinjam) as year, MONTH(tgl_pinjam) as month, MONTHNAME(tgl_pinjam) as month_name, COUNT(*) as count'))
            ->where('tgl_pinjam', '>=', now()->subMonths(6))
            ->groupBy('year', 'month', 'month_name')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $viewData['loanChartLabels'] = $loanStats->pluck('month_name');
        $viewData['loanChartData'] = $loanStats->pluck('count');
    } else {
        $userId = Auth::id();
        $semuaPeminjamanUser = \App\Models\Peminjaman::where('id_user', $userId)
            ->with('buku')
            ->latest('tgl_pinjam')
            ->get();

        $viewData['sedangDipinjam'] = $semuaPeminjamanUser->where('status', 'pinjam');
        $viewData['totalDibaca'] = $semuaPeminjamanUser->where('status', 'kembali')->count();
        // Menggunakan kolom denda dari database
        $viewData['totalDenda'] = $semuaPeminjamanUser->sum('denda');
        $viewData['bukuPopuler'] = \App\Models\Buku::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc')
            ->take(5)
            ->get();
    }

    return view('dashboard', $viewData);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil/riwayat-peminjaman', [ProfileController::class, 'myBorrowingHistory'])->name('profile.history');

    // Menu Umum (Bisa diakses user login)
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');

    // Menu Khusus Admin
    Route::middleware('is.admin')->group(function () {

        // Peminjaman (Urutan Penting: Custom Route dulu baru Resource)
        Route::post('/peminjaman/{peminjaman}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])->name('peminjaman.bayarDenda');
        Route::resource('peminjaman', PeminjamanController::class)->except(['destroy']);

        // Resource Lain
        Route::resource('penerbit', PenerbitController::class)->except(['index', 'show']);
        Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::resource('penulis', PenulisController::class)->except(['show']);

        // Buku Custom Actions
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');

        // User Custom Actions
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

        // Laporan & Download
        Route::get('/laporan/peminjaman/cetak', [LaporanController::class, 'cetakPeminjaman'])->name('laporan.peminjaman.cetak');
        Route::get('/buku/download', [BukuController::class, 'downloadPDF'])->name('buku.download');
        Route::get('/penerbit/download', [PenerbitController::class, 'downloadPDF'])->name('penerbit.download');
        Route::get('/users/download', [UserController::class, 'downloadPDF'])->name('users.download');
    });
});

require __DIR__ . '/auth.php';
