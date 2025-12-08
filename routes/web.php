<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenerbitController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenulisController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // === [1. PROFILE] ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil/riwayat-peminjaman', [ProfileController::class, 'myBorrowingHistory'])->name('profile.history');

    Route::middleware('is.admin')->group(function () {

        // --- BUKU (Admin Only Routes) ---
        Route::get('/penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');

        // --- TRANSAKSI & LAINNYA ---
        Route::post('/peminjaman/{peminjaman}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])->name('peminjaman.bayarDenda');
        Route::resource('peminjaman', PeminjamanController::class)->except(['destroy']);

        // Resource Master Data Lain
        Route::resource('penerbit', PenerbitController::class)->except(['index', 'show']);
        Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
        Route::resource('penulis', PenulisController::class)->except(['show']);

        // User Management
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

        // Laporan & Download
        Route::get('/laporan/peminjaman/cetak', [LaporanController::class, 'cetakPeminjaman'])->name('laporan.peminjaman.cetak');
        Route::get('/penerbit/download', [PenerbitController::class, 'downloadPDF'])->name('penerbit.download');
        Route::get('/users/download', [UserController::class, 'downloadPDF'])->name('users.download');
    });


    Route::get('/buku/download', [BukuController::class, 'downloadPDF'])->name('buku.download');
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');

});

require __DIR__ . '/auth.php';