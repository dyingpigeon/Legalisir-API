<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataAlumniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nim' => 'required|integer|unique:dataalumnis,nim',
            'email' => 'required|email|unique:dataalumnis,email',
            'nik' => 'required|integer|unique:dataalumnis,nik',
            'nama' => 'required|string|max:255',
            'jk' => 'required|in:laki-laki,perempuan',
            'nama_Ibu' => 'required|string|max:255',
            'agama' => 'required|string|max:50',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'hp' => 'required|string|max:20',
            'nomor_Ijazah_Elektronik' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        // Tidak perlu prepareForValidation karena field name sudah sama
        // dengan database column name
    }
}