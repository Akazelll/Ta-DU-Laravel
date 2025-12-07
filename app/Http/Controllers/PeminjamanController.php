<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'tanggal_harus_kembali' => Carbon::parse($request->tgl_pinjam)->addDays(7),
                'status' => 'pinjam',
                'denda' => 0,
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

    // [PERBAIKAN UTAMA ADA DI SINI]
    public function update(Request $request, string $id)
    {
        if ($request->status == 'kembali') {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::findOrFail($id);

                // Jika status sudah kembali, hentikan proses agar tidak update berulang
                if ($peminjaman->status === 'kembali') {
                    return;
                }

                $peminjaman->tgl_kembali = now();
                $peminjaman->status = 'kembali';

                // Hitung Denda
                $tglHarusKembali = Carbon::parse($peminjaman->tanggal_harus_kembali)->startOfDay();
                $tglKembali = now()->startOfDay();

                $dendaFinal = 0;

                // Cek apakah terlambat (hanya jika tanggal kembali > tanggal harus kembali)
                if ($tglKembali->gt($tglHarusKembali)) {
                    $hariTerlambat = $tglHarusKembali->diffInDays($tglKembali);
                    // Gunakan konstanta dari Model agar konsisten (5000)
                    $dendaFinal = $hariTerlambat * Peminjaman::DENDA_PER_HARI;
                }

                $peminjaman->denda = $dendaFinal;

                // Cek Status Pembayaran Denda
                // Ambil nilai denda_dibayar (default 0 jika null)
                $sudahDibayar = $peminjaman->denda_dibayar ?? 0;

                if ($sudahDibayar >= $dendaFinal) {
                    $peminjaman->status_denda = 'Lunas';
                } else {
                    $peminjaman->status_denda = 'Belum Lunas';
                }

                $peminjaman->save();

                // Kembalikan stok buku
                $buku = Buku::withTrashed()->find($peminjaman->id_buku);
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

    public function show(string $id)
    {
        return redirect()->route('peminjaman.index');
    }

    public function destroy(string $id) {}
}
