<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIjazahRequest extends FormRequest
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
            'nomorIjazah' => 'required|integer|unique:ijazahs,nomor_ijazah',
            'nim' => 'required|integer',
            'pathFile' => 'required|string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nomor_ijazah' => $this->nomorIjazah,
            'path_file' => $this->pathFile,
        ]);
    }
}