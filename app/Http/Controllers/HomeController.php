<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Penerbit;
use App\Models\Buku;
use App\Models\User;
use App\Models\Peminjaman;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $hour = now('Asia/Jakarta')->hour;
        $greeting = match (true) {
            $hour < 11 => 'Selamat Pagi 🌄',
            $hour < 15 => 'Selamat Siang 🌞',
            $hour < 19 => 'Selamat Sore 🌤️',
            default => 'Selamat Malam 🌙',
        };

        $viewData = ['greeting' => $greeting];

        if (Auth::user()->role == 'admin') {
            $viewData['totalPenerbit'] = Penerbit::count();
            $viewData['totalBuku'] = Buku::count();
            $viewData['totalUser'] = User::count();
            $viewData['peminjamanAktif'] = Peminjaman::where('status', 'pinjam')->count();

            $viewData['bukuPopuler'] = Buku::withCount('peminjaman')
                ->orderBy('peminjaman_count', 'desc')
                ->take(5)
                ->get();

            $viewData['anggotaAktif'] = User::withCount('peminjaman')
                ->orderBy('peminjaman_count', 'desc')
                ->take(5)
                ->get();

            // Statistik Bulanan (MySQL Compatible)
            $loanStats = Peminjaman::select(
                DB::raw('YEAR(tgl_pinjam) as year'),
                DB::raw('MONTH(tgl_pinjam) as month'),
                DB::raw('MONTHNAME(tgl_pinjam) as month_name'),
                DB::raw('COUNT(*) as count')
            )
                ->where('tgl_pinjam', '>=', now()->subMonths(6))
                ->groupBy('year', 'month', 'month_name')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            $viewData['loanChartLabels'] = $loanStats->pluck('month_name');
            $viewData['loanChartData'] = $loanStats->pluck('count');
        } else {
            // User Dashboard Logic
            $userId = Auth::id();
            $semuaPeminjamanUser = Peminjaman::where('id_user', $userId)
                ->with('buku')
                ->latest('tgl_pinjam')
                ->get();

            $viewData['sedangDipinjam'] = $semuaPeminjamanUser->where('status', 'pinjam');
            $viewData['totalDibaca'] = $semuaPeminjamanUser->where('status', 'kembali')->count();
            $viewData['totalDenda'] = $semuaPeminjamanUser->sum('denda');

            $viewData['bukuPopuler'] = Buku::withCount('peminjaman')
                ->orderBy('peminjaman_count', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard', $viewData);
    }
}
