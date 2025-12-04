<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataAlumniRequest extends FormRequest
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
        // Gunakan parameter yang benar
        $id = $this->route('data_alumnus');

        if (!$id) {
            abort(422, 'ID tidak ditemukan dalam route parameters');
        }

        $method = $this->method();

        if ($method == 'PUT') {
            return [
                'nim' => ['required', 'integer', 'unique:dataalumnis,nim,' . $id],
                'email' => ['required', 'email', 'unique:dataalumnis,email,' . $id],
                'nik' => ['required', 'integer', 'unique:dataalumnis,nik,' . $id],
                'nama' => ['required', 'string', 'max:255'],
                'jk' => ['required', 'in:laki-laki,perempuan'],
                'nama_Ibu' => ['required', 'string', 'max:255'],
                'agama' => ['required', 'string', 'max:50'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'alamat' => ['required', 'string'],
                'hp' => ['required', 'string', 'max:20'],
                'nomor_Ijazah_Elektronik' => ['required', 'string'],
                'is_active' => ['required', 'boolean'],
            ];
        } else {
            return [
                'nim' => ['sometimes', 'integer', 'unique:dataalumnis,nim,' . $id],
                'email' => ['sometimes', 'email', 'unique:dataalumnis,email,' . $id],
                'nik' => ['sometimes', 'integer', 'unique:dataalumnis,nik,' . $id],
                'nama' => ['sometimes', 'string', 'max:255'],
                'jk' => ['sometimes', 'in:laki-laki,perempuan'],
                'nama_Ibu' => ['sometimes', 'string', 'max:255'],
                'agama' => ['sometimes', 'string', 'max:50'],
                'tempat_lahir' => ['sometimes', 'string', 'max:100'],
                'tanggal_lahir' => ['sometimes', 'date'],
                'alamat' => ['sometimes', 'string'],
                'hp' => ['sometimes', 'string', 'max:20'],
                'nomor_Ijazah_Elektronik' => ['sometimes', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        // Tidak perlu prepareForValidation karena field name sudah sama
        // dengan database column name
    }
}