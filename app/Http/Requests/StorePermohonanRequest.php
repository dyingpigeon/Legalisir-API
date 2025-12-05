<?php

namespace App\Http\Requests;

use App\Models\DataAlumni;
use App\Models\Ijazah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePermohonanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya user dengan role 'user' yang bisa membuat permohonan
        return Auth::check() && Auth::user()->role === 'user';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'nomorIjazah' => 'required|integer',
            'nomorIjazah' => [
                'required',
                'string',
                'min:10', // sesuaikan dengan format nomor ijazah
                'max:50',
                // Validasi apakah nomor ijazah ada di data alumni
                function ($attribute, $value, $fail) {
                    $user = Auth::user();

                    // Cari data alumni berdasarkan nomor ijazah
                    $alumni = DataAlumni::where('nomor_ijazah', $value)->first();
                    $ijazah = Ijazah::where('nomor_ijazah', $value)->first();

                    // Validasi 1: Nomor ijazah harus ada
                    if ($ijazah->nim !== $user->username) {
                        $fail('Nomor ijazah tidak ditemukan dalam data alumni.');
                        return;
                    }

                    // Validasi 2: NIK harus cocok
                    if ($alumni->nik !== $user->nik) {
                        $fail('Nomor ijazah ini bukan milik Anda berdasarkan data NIK.');
                        return;
                    }

                    // Validasi 3: NIM harus cocok
                    if ($alumni->nim !== $user->username) {
                        $fail('Nomor ijazah ini bukan milik Anda berdasarkan data NIM.');
                        return;
                    }
                }
            ],
            'jumlahLembar' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'file' => 'required|string',
            // Hapus userId dan username karena akan diambil dari user yang login
            // 'userId' => 'required|integer|exists:users,id',
            // 'username' => 'required|integer',
            // 'status' => 'sometimes|integer|min:1|max:5',
            'tanggalDiambil' => 'sometimes|nullable|date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // Ambil user_id dan username dari user yang sedang login
            'user_id' => Auth::id(),
            'username' => Auth::user()->username,
            'nomor_ijazah' => $this->nomorIjazah,
            'jumlah_lembar' => $this->jumlahLembar,
            'tanggal_diambil' => $this->tanggalDiambil,
            'status' => 1,
        ]);
    }

    /**
     * Custom message for authorization
     */
    public function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Hanya user biasa yang dapat membuat permohonan.'
        );
    }
}