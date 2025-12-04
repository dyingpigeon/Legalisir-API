<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'nomor_ijazah',
        'jumlah_lembar',
        'keperluan',
        'file',
        'status',
        'tanggal_diambil'
    ];

    protected $casts = [
        'tanggal_diambil' => 'datetime'
    ];

    // Konstanta status
    const STATUS_DIMULAI = 1;
    const STATUS_DITERIMA = 2;
    const STATUS_VERIFIKASI = 3;
    const STATUS_DITANDATANGANI = 4;
    const STATUS_SIAP_DIAMBIL = 5;
    const STATUS_SUDAH_DIAMBIL = 6;  // Diubah dari DITOLAK
    const DITOLAK = 7;               // DITOLAK dipindah ke status 7

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan ijazah
    public function ijazah()
    {
        return $this->belongsTo(Ijazah::class, 'nomor_ijazah', 'nomor_ijazah');
    }

    // Relasi dengan riwayat status
    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    // Accessor untuk status text
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_DIMULAI => 'Dimulai',
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_VERIFIKASI => 'Verifikasi',
            self::STATUS_DITANDATANGANI => 'Ditandatangani',
            self::STATUS_SIAP_DIAMBIL => 'Siap Diambil',
            self::STATUS_SUDAH_DIAMBIL => 'Sudah Diambil',  // Diubah
            self::DITOLAK => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    public static function getStatusLabel($status)
    {
        return match ($status) {
            self::STATUS_DIMULAI => 'Dimulai',
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_VERIFIKASI => 'Verifikasi',
            self::STATUS_DITANDATANGANI => 'Ditandatangani',
            self::STATUS_SIAP_DIAMBIL => 'Siap Diambil',
            self::STATUS_SUDAH_DIAMBIL => 'Sudah Diambil',  // Diubah
            self::DITOLAK => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }
}