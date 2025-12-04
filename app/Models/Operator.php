<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nik',
        'nama',
        'jk'
    ];

    protected $casts = [
        'nip' => 'integer',
        'nik' => 'integer'
    ];

    public function userByNip()
    {
        return $this->hasOne(User::class, 'username', 'nip');
    }

    public function userByNik()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }
}