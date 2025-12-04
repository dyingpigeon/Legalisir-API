<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIjazahRequest extends FormRequest
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
                'nomorIjazah' => ['required', 'integer', 'unique:ijazahs,nomor_ijazah,' . $this->ijazah->id],
                'nim' => ['required', 'integer'],
                'pathFile' => ['required', 'string'],
            ];
        } else {
            return [
                'nomorIjazah' => ['sometimes', 'integer', 'unique:ijazahs,nomor_ijazah,' . $this->ijazah->id],
                'nim' => ['sometimes', 'integer'],
                'pathFile' => ['sometimes', 'string'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        if ($this->has('nomorIjazah')) {
            $this->merge(['nomor_ijazah' => $this->nomorIjazah]);
        }
        if ($this->has('pathFile')) {
            $this->merge(['path_file' => $this->pathFile]);
        }
    }
}