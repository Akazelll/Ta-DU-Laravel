<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Data Buku - DigiPustaka</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        @page {
            margin: 100px 40px 50px 40px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 60px;
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 40px;
            font-size: 9px;
            color: #888;
            text-align: center;
        }

        .page-number:before {
            content: "Halaman " counter(page);
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #444;
        }

        .report-subtitle {
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 0;
        }

        .report-period {
            font-size: 10px;
            margin-top: 5px;
            color: #777;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .main-table thead th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 20px;
        }
        
        .w-no { width: 5%; }
        .w-kode { width: 12%; }
        .w-judul { width: auto; }
        .w-penulis { width: 15%; }
        .w-kategori { width: 12%; }
        .w-penerbit { width: 15%; }
        .w-stok { width: 8%; }
    </style>
</head>

<body>
    <footer>
        Laporan ini dicetak secara otomatis oleh Sistem DigiPustaka pada
        {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y, HH:mm') }} | <span class="page-number"></span>
    </footer>

    <header>
        <div class="report-title">DigiPustaka</div>
        <div class="report-subtitle">Laporan Data Buku</div>
        <div class="report-period">Dicetak pada {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}</div>
    </header>

    <main>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="w-no">No</th>
                    <th class="w-kode">Kode</th>
                    <th class="w-judul">Judul Buku</th>
                    <th class="w-penulis">Penulis</th>
                    <th class="w-kategori">Kategori</th>
                    <th class="w-penerbit">Penerbit</th>
                    <th class="w-stok">Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buku as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $item->kode_buku }}</td>
                        <td>{{ $item->judul_buku }}</td>
                        <td>{{ $item->penulis->nama_penulis ?? '-' }}</td>
                        <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->penerbit->nama_penerbit ?? '-' }}</td>
                        <td class="text-center">{{ $item->stok }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="no-data">Data buku tidak tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>

</html>