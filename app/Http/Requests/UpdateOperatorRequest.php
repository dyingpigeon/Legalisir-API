<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperatorRequest extends FormRequest
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
        $method = $this->method();

        if ($method == 'PUT') {
            return [
                'nip' => ['required', 'integer', 'unique:operators,nip,' . $this->operator->id],
                'nik' => ['required', 'integer', 'unique:operators,nik,' . $this->operator->id],
                'nama' => ['required', 'string', 'max:255'],
                'jk' => ['required', 'in:laki-laki,perempuan'],
            ];
        } else {
            return [
                'nip' => ['sometimes', 'integer', 'unique:operators,nip,' . $this->operator->id],
                'nik' => ['sometimes', 'integer', 'unique:operators,nik,' . $this->operator->id],
                'nama' => ['sometimes', 'string', 'max:255'],
                'jk' => ['sometimes', 'in:laki-laki,perempuan'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        // Tidak perlu prepareForValidation karena field name sudah sama
        // dengan database column name
    }
}