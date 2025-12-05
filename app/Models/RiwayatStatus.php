<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'user_id',
        'status_sebelum',
        'status_sesudah',
        'keterangan'
    ];

    // Relasi
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk status text
    public function getStatusSebelumTextAttribute()
    {
        if ($this->status_sebelum === null)
            return null;

        return match ($this->status_sebelum) {
            Permohonan::STATUS_DIMULAI => 'Dimulai',
            Permohonan::STATUS_VERIFIKASI => 'Verifikasi',
            Permohonan::STATUS_DITANDATANGANI => 'Ditandatangani',
            Permohonan::STATUS_SIAP_DIAMBIL => 'Siap Diambil',
            Permohonan::STATUS_SUDAH_DIAMBIL => 'Sudah Diambil',
            Permohonan::DITOLAK => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    public function getStatusSesudahTextAttribute()
    {
        return match ($this->status_sesudah) {
            Permohonan::STATUS_DIMULAI => 'Dimulai',
            Permohonan::STATUS_VERIFIKASI => 'Verifikasi',
            Permohonan::STATUS_DITANDATANGANI => 'Ditandatangani',
            Permohonan::STATUS_SIAP_DIAMBIL => 'Siap Diambil',
            Permohonan::STATUS_SUDAH_DIAMBIL => 'Sudah Diambil',
            Permohonan::DITOLAK => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }
}