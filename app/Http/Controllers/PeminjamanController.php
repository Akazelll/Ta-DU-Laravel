<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->latest('tgl_pinjam')->paginate(12);
        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $buku = Buku::orderBy('judul_buku')->get();

        return view('peminjaman.create', compact('users', 'buku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_buku' => 'required|exists:buku,id',
            'tgl_pinjam' => 'required|date',
        ]);

        $buku = Buku::findOrFail($request->id_buku);

        if ($buku->stok < 1) {
            return redirect()->back()->withErrors(['id_buku' => 'Stok buku habis.'])->withInput();
        }

        DB::transaction(function () use ($request, $buku) {
            Peminjaman::create([
                'id_user' => $request->id_user,
                'id_buku' => $request->id_buku,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tanggal_harus_kembali' => \Carbon\Carbon::parse($request->tgl_pinjam)->addDays(7),
                'status' => 'pinjam',
                'denda' => 0, // [FIX] Set default denda 0
                'denda_dibayar' => 0
            ]);
            $buku->decrement('stok');
        });

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        return redirect()->route('peminjaman.index')->with('error', 'Fitur edit belum tersedia.');
    }

    public function update(Request $request, string $id)
    {
        if ($request->status == 'kembali') {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::findOrFail($id);

                if ($peminjaman->status === 'kembali') {
                    return;
                }

                $dendaFinal = 0;
                if (now()->gt($peminjaman->tanggal_harus_kembali)) {
                    $tanggalHarusKembali = \Carbon\Carbon::parse($peminjaman->tanggal_harus_kembali)->startOfDay();
                    $tanggalKembali = now()->startOfDay();
                    if ($tanggalKembali->gt($tanggalHarusKembali)) {
                        $hariTerlambat = $tanggalHarusKembali->diffInDays($tanggalKembali);
                        $dendaPerHari = defined('App\Models\Peminjaman::DENDA_PER_HARI') ? Peminjaman::DENDA_PER_HARI : 1000;
                        $dendaFinal = $hariTerlambat * $dendaPerHari;
                    }
                }

                $peminjaman->status = 'kembali';
                $peminjaman->tgl_kembali = now();

                // [FIX] Menggunakan nama kolom yang benar: 'denda'
                $peminjaman->denda = $dendaFinal;

                // Cek status lunas (pastikan kolom denda_dibayar sudah ada di DB)
                $sudahDibayar = $peminjaman->denda_dibayar ?? 0;

                if ($sudahDibayar >= $dendaFinal) {
                    $peminjaman->status_denda = 'Lunas';
                } else {
                    $peminjaman->status_denda = 'Belum Lunas';
                }

                $peminjaman->save();

                // Kembalikan stok buku
                $buku = Buku::find($peminjaman->id_buku);
                if ($buku) {
                    $buku->increment('stok');
                }
            });

            return redirect()->back()->with('success', 'Buku telah berhasil dikembalikan.');
        }

        return redirect()->route('peminjaman.index');
    }

    public function bayarDenda(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'jumlah_bayar' => 'required|integer|min:1'
        ]);

        $jumlahBayar = (int) $request->jumlah_bayar;

        // [FIX] Menggunakan nama kolom yang benar: 'denda'
        $totalDenda = $peminjaman->denda;
        $sudahDibayar = $peminjaman->denda_dibayar ?? 0;
        $sisaDenda = $totalDenda - $sudahDibayar;

        if ($jumlahBayar > $sisaDenda) {
            return redirect()->back()->withErrors(['error' => 'Jumlah pembayaran melebihi sisa denda.']);
        }

        $peminjaman->denda_dibayar = $sudahDibayar + $jumlahBayar;

        if ($peminjaman->denda_dibayar >= $totalDenda) {
            $peminjaman->status_denda = 'Lunas';
        }

        $peminjaman->save();
        return redirect()->back()->with('success', 'Pembayaran denda berhasil dicatat.');
    }

    public function show(string $id) {
        return redirect()->route('peminjaman.index');
    }
    public function destroy(string $id) {}
}
