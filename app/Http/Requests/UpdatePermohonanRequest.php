<?php

namespace App\Http\Requests;

use App\Models\DataAlumni;
use App\Models\Ijazah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePermohonanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya user dengan role 'user' yang bisa mengupdate permohonan
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
            'nomor_ijazah' => [
                'required',
                'string',
                'min:10',
                'max:50',
                // Validasi apakah nomor ijazah ada di data alumni
                function ($attribute, $value, $fail) {
                    $user = Auth::user();

                    // Cari data alumni berdasarkan nomor ijazah
                    $alumni = DataAlumni::where('nomor_ijazah', $value)->first();
                    $ijazah = Ijazah::where('nomor_ijazah', $value)->first();

                    // Validasi 1: Data ijazah harus ada
                    if (!$ijazah) {
                        $fail('Nomor ijazah tidak ditemukan dalam data ijazah.');
                        return;
                    }

                    // Validasi 2: Data alumni harus ada
                    if (!$alumni) {
                        $fail('Nomor ijazah tidak ditemukan dalam data alumni.');
                        return;
                    }

                    // Validasi 3: NIK harus cocok
                    if ($alumni->nik !== $user->nik) {
                        $fail('Nomor ijazah ini bukan milik Anda berdasarkan data NIK.');
                        return;
                    }

                    // Validasi 4: NIM harus cocok
                    if ($alumni->nim !== $user->username) {
                        $fail('Nomor ijazah ini bukan milik Anda berdasarkan data NIM.');
                        return;
                    }

                    // Validasi 5: NIM di tabel ijazah harus cocok
                    if ($ijazah->nim !== $user->username) {
                        $fail('Nomor ijazah ini bukan milik Anda berdasarkan data NIM di tabel ijazah.');
                        return;
                    }
                }
            ],
            'jumlah_lembar' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'file' => ['sometimes', 'file', 'mimes:pdf,doc,docx,zip,rar,txt,jpg,jpeg,png', 'max:10240'],
            // 'status' => 'sometimes|integer|min:1|max:5', // status biasanya tidak diupdate oleh user
            'tanggal_diambil' => 'sometimes|nullable|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nomor_ijazah' => 'nomor ijazah',
            'jumlah_lembar' => 'jumlah lembar',
            'tanggal_diambil' => 'tanggal diambil',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nomor_ijazah.required' => 'Nomor ijazah wajib diisi.',
            'nomor_ijazah.string' => 'Nomor ijazah harus berupa teks.',
            'nomor_ijazah.min' => 'Nomor ijazah minimal 10 karakter.',
            'nomor_ijazah.max' => 'Nomor ijazah maksimal 50 karakter.',
            'jumlah_lembar.required' => 'Jumlah lembar wajib diisi.',
            'jumlah_lembar.integer' => 'Jumlah lembar harus berupa angka.',
            'jumlah_lembar.min' => 'Jumlah lembar minimal 1.',
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.string' => 'Keperluan harus berupa teks.',
            'file.required' => 'File wajib diunggah.',
            'file.file' => 'File harus berupa berkas.',
            'file.mimes' => 'File harus berupa PDF, DOC, DOCX, ZIP, RAR, TXT, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'tanggal_diambil.date' => 'Tanggal diambil harus berupa tanggal yang valid.',
        ];
    }

    /**
     * Custom message for authorization
     */
    public function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Hanya user biasa yang dapat mengupdate permohonan.'
        );
    }
}