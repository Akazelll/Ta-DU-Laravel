<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    const DENDA_PER_HARI = 5000;

    protected $fillable = [
        'id_user',
        'id_buku',
        'tgl_pinjam',
        'tanggal_harus_kembali',
        'tgl_kembali',
        'status',
        'denda',
        'denda_dibayar',
        'status_denda'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku')->withTrashed();
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tanggal_harus_kembali
                && now()->startOfDay()->gt(Carbon::parse($this->tanggal_harus_kembali)->startOfDay())
                && $this->status == 'pinjam'
        );
    }

    protected function sisaDenda(): Attribute
    {
        return Attribute::make(
            get: function () {
                $totalDenda = 0;

                if ($this->status == 'kembali') {
                    $totalDenda = $this->denda;
                }
                elseif ($this->is_overdue) {
                    $tanggalHarusKembali = Carbon::parse($this->tanggal_harus_kembali)->startOfDay();
                    $sekarang = now()->startOfDay();

                    if ($sekarang->gt($tanggalHarusKembali)) {
                        $hariTerlambat = $tanggalHarusKembali->diffInDays($sekarang);
                        $totalDenda = $hariTerlambat * self::DENDA_PER_HARI;
                    }
                }

                $sisa = $totalDenda - ($this->denda_dibayar ?? 0);
                return $sisa > 0 ? $sisa : 0;
            }
        );
    }
}
