<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Penerbit;
use App\Models\Kategori;
use App\Models\Penulis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use Illuminate\Support\Str;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $sortableColumns = ['judul_buku', 'tahun_terbit', 'stok', 'created_at'];
        $sortBy = in_array($request->query('sort_by'), $sortableColumns) ? $request->query('sort_by') : 'created_at';
        $sortDirection = in_array($request->query('sort_direction'), ['asc', 'desc']) ? $request->query('sort_direction') : 'desc';
        $search = $request->input('search');
        $kategoriFilter = $request->input('kategori');

        $buku = Buku::with(['penerbit', 'kategori', 'penulis']) 
            ->when($search, function ($query, $search) {
                return $query->where('judul_buku', 'like', "%{$search}%")
                    ->orWhereHas('penerbit', function ($q) use ($search) {
                        $q->where('nama_penerbit', 'like', "%{$search}%");
                    });
            })
            ->when($kategoriFilter, function ($query, $kategoriFilter) {
                return $query->whereHas('kategori', function ($q) use ($kategoriFilter) {
                    $q->where('nama_kategori', $kategoriFilter);
                });
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('buku.index', compact('buku', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $penerbit = Penerbit::all();
        $kategori = Kategori::all();
        $penulis = Penulis::all();

        return view('buku.create', compact('penerbit', 'kategori', 'penulis'));
    }

    public function store(StoreBukuRequest $request)
    {
        $validatedData = $request->validated();

        // Generate Kode Buku Unik
        do {
            // [FIX] Ganti String::random menjadi Str::random
            $kodeBuku = strtoupper(Str::random(5)) . mt_rand(10000, 99999);
        } while (Buku::where('kode_buku', $kodeBuku)->exists());

        $validatedData['kode_buku'] = $kodeBuku;

        if ($request->hasFile('sampul')) {
            $path = $request->file('sampul')->store('sampul_buku', 'public');
            $validatedData['sampul'] = $path;
        }

        Buku::create($validatedData);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil disimpan.');
    }

    public function show(Buku $buku)
    {
        return redirect()->route('buku.edit', $buku);
    }

    public function edit(Buku $buku)
    {
        $penerbit = Penerbit::all();
        $kategori = Kategori::all();
        $penulis = Penulis::all(); // [FIX] Ambil data penulis untuk form edit

        return view('buku.edit', compact('buku', 'penerbit', 'kategori', 'penulis'));
    }

    public function update(UpdateBukuRequest $request, Buku $buku)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('sampul')) {
            if ($buku->sampul) {
                Storage::disk('public')->delete($buku->sampul);
            }
            $path = $request->file('sampul')->store('sampul_buku', 'public');
            $validatedData['sampul'] = $path;
        }

        $buku->update($validatedData);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diubah.');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->back()->with('success', 'Buku berhasil dihapus.');
    }

    public function downloadPDF()
    {
        $buku = Buku::with(['penerbit', 'kategori', 'penulis'])->orderBy('judul_buku', 'asc')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('buku.pdf', compact('buku'));
        return $pdf->stream('laporan-data-buku.pdf');
    }
}
