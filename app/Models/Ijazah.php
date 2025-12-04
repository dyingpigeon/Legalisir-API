<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ijazah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_ijazah',
        'nim',
        'path_file'
    ];

    // Relasi dengan data alumni
    public function alumni()
    {
        return $this->belongsTo(DataAlumni::class, 'nim', 'nim');
    }

    // Relasi dengan permohonan
    public function permohonans()
    {
        return $this->hasMany(Permohonan::class, 'nomor_ijazah', 'nomor_ijazah');
    }
}