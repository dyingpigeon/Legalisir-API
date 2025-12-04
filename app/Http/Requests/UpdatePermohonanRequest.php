<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermohonanRequest extends FormRequest
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
                'userId' => ['required', 'integer', 'exists:users,id'],
                'username' => ['required', 'integer'],
                'nomorIjazah' => ['required', 'integer'],
                'jumlahLembar' => ['required', 'integer', 'min:1'],
                'keperluan' => ['required', 'string'],
                'file' => ['required', 'string'],
                'status' => ['required', 'integer', 'min:1', 'max:5'],
                'tanggalDiambil' => ['sometimes', 'nullable', 'date'],
            ];
        } else {
            return [
                'userId' => ['sometimes', 'integer', 'exists:users,id'],
                'username' => ['sometimes', 'integer'],
                'nomorIjazah' => ['sometimes', 'integer'],
                'jumlahLembar' => ['sometimes', 'integer', 'min:1'],
                'keperluan' => ['sometimes', 'string'],
                'file' => ['sometimes', 'string'],
                'status' => ['sometimes', 'integer', 'min:1', 'max:5'],
                'tanggalDiambil' => ['sometimes', 'nullable', 'date'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        if ($this->has('userId')) {
            $this->merge(['user_id' => $this->userId]);
        }
        if ($this->has('nomorIjazah')) {
            $this->merge(['nomor_ijazah' => $this->nomorIjazah]);
        }
        if ($this->has('jumlahLembar')) {
            $this->merge(['jumlah_lembar' => $this->jumlahLembar]);
        }
        if ($this->has('tanggalDiambil')) {
            $this->merge(['tanggal_diambil' => $this->tanggalDiambil]);
        }
    }
}