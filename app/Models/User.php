<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'nik',
        'password',
        'role',
        'remember_token',
        'verification_link_used_at',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'username' => 'integer',
        'nik' => 'integer',
    ];

    // Scope untuk role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // ini yang belongs
    // Belongs to alumni
    public function alumniByUsername()
    {
        return $this->belongsTo(DataAlumni::class, 'username', 'nim');
    }

    public function alumniByNik()
    {
        return $this->belongsTo(DataAlumni::class, 'nik', 'nik');
    }

    public function alumniByEmail()
    {
        return $this->belongsTo(DataAlumni::class, 'email', 'email');
    }

    // belongs to operator
    public function operatorByNip()
    {
        return $this->belongsTo(Operator::class, 'username', 'nip');
    }

    public function operatorByNik()
    {
        return $this->belongsTo(Operator::class, 'nik', 'nik');
    }

    // hasmany hasone
    public function permohonans()
    {
        return $this->hasMany(Permohonan::class);
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    /**
     * Determine if the user has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new \App\Notifications\CustomVerifyEmail);
    // }
}