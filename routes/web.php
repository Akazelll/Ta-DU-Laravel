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

// Pindahkan logika Dashboard ke HomeController agar lebih rapi
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // === [1. PROFILE & HISTORY] ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil/riwayat-peminjaman', [ProfileController::class, 'myBorrowingHistory'])->name('profile.history');

    // === [2. AKSES UMUM (User & Admin)] ===
    // User biasa perlu akses ini untuk melihat daftar buku/kategori/penerbit

    // Route Download (Letakkan paling atas sebelum resource buku)
    Route::get('/buku/download', [BukuController::class, 'downloadPDF'])->name('buku.download');

    // Buku: Hanya Index dan Show yang boleh diakses umum
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');

    // Kategori & Penerbit (Read Only)
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');


    // === [3. AREA KHUSUS ADMIN] ===
    Route::middleware('is.admin')->group(function () {

        // --- TRANSAKSI PEMINJAMAN ---
        // Custom route untuk bayar denda (harus sebelum resource)
        Route::post('/peminjaman/{peminjaman}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])
            ->name('peminjaman.bayarDenda');

        Route::resource('peminjaman', PeminjamanController::class)->except(['destroy']);


        // --- MANAJEMEN DATA MASTER (CRUD) ---
        // Menggunakan except(['index', 'show']) karena index & show sudah didefinisikan di atas (Akses Umum)

        // Buku: Admin punya akses Create, Store, Edit, Update, Destroy
        Route::resource('buku', BukuController::class)->except(['index', 'show']);

        // Resource Lainnya
        Route::resource('penerbit', PenerbitController::class)->except(['index', 'show']);
        Route::resource('kategori', KategoriController::class)->except(['index', 'show']);
        Route::resource('penulis', PenulisController::class)->except(['show']);

        // User Management
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');


        // --- LAPORAN & DOWNLOAD ---
        Route::get('/laporan/peminjaman/cetak', [LaporanController::class, 'cetakPeminjaman'])->name('laporan.peminjaman.cetak');
        Route::get('/penerbit/download', [PenerbitController::class, 'downloadPDF'])->name('penerbit.download');
        Route::get('/users/download', [UserController::class, 'downloadPDF'])->name('users.download');
    });
});

require __DIR__ . '/auth.php';
