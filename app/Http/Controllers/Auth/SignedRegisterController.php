<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DataAlumni;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Str;

class SignedRegisterController extends Controller
{
    /**
     * menerima input NIK NIM dan NAMA IBU
     */

    public function askForRegister(Request $request): JsonResponse
    {
        // validasi data yang dimasukkan
        $request->validate([
            'nik' => ['required', 'string', Rule::unique('users', 'nik'), Rule::exists('dataalumnis', 'nik')],
            'username' => ['required', 'string', Rule::unique('users', 'username'), Rule::exists('dataalumnis', 'nim')],
            'namaIbu' => ['required', 'string', Rule::exists('dataalumnis', 'nama_ibu')],
        ]);

        // Cek apakah data benar-benar match di tabel dataalumnis
        $dataalumnus = DataAlumni::where('nik', $request->nik)
            ->where('nim', $request->username)
            ->where('nama_ibu', $request->namaIbu)
            ->first();

        // Validasi tambahan: pastikan data ditemukan dan match
        if (!$dataalumnus) {
            return response()->json([
                'message' => 'Data tidak ditemukan atau tidak sesuai.',
                'errors' => [
                    'nik' => ['Silakan periksa NIK, NIM, dan nama ibu.']
                ]
            ], 422);
        }

        // Generate password
        $raw = Str::password(32);
        $hashed = Hash::make($raw);
        $email = $dataalumnus->email;
        $maskedEmail = $this->maskEmail($email);

        // Create user
        $user = User::create([
            'username' => $dataalumnus->nim,
            'name' => $dataalumnus->nama,
            'nik' => $dataalumnus->nik,
            'email' => $email,
            'password' => $hashed
        ]);

        // ✅ TRIGGER EMAIL VERIFICATION
        event(new Registered($user));

        return response()->json([
            'message' => "Registrasi berhasil! Silakan cek email {$maskedEmail} Anda untuk verifikasi.",
            'user_id' => $user->id,
            'email_sent' => true
        ], 201);
    }

    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1];

        // Sensor sebagian username (tampilkan 2 karakter pertama dan terakhir)
        $maskedUsername = strlen($username) > 4
            ? substr($username, 0, 2) . '***' . substr($username, -2)
            : substr($username, 0, 1) . '***';

        return $maskedUsername . '@' . $domain;
    }
    // TODO: buat vverify data masuk dan pake regis


    // public function verifyForRegister(Request $request)
    // {
    //     if (!$request->hasValidSignature()) {
    //         return response()->json([
    //             'message' => 'Invalid or expired link'
    //         ], 403);
    //     }

    //     $request->validate([

    //     ]);
    // }
}