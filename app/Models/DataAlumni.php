<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAlumni extends Model
{
    use HasFactory;

    protected $table = 'dataalumnis';

    protected $fillable = [
        'nim',
        'email',
        'nik',
        'nama',
        'jk',
        'nama_Ibu',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'hp',
        'nomor_ijazah',
        'is_active'
    ];

    protected $casts = [
        'nim' => 'integer',
        'nik' => 'integer',
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    // hasOne
    public function ijazah()
    {
        return $this->hasOne(Ijazah::class, 'nim', 'nim');
    }

    public function userByUsername()
    {
        return $this->hasOne(User::class, 'username', 'nim');
    }

    public function userByNik()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }

    public function userByEmail()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}