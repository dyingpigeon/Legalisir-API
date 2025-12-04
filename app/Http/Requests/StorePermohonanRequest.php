<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanRequest extends FormRequest
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
            'userId' => 'required|integer|exists:users,id',
            'username' => 'required|integer',
            'nomorIjazah' => 'required|integer',
            'jumlahLembar' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'file' => 'required|string',
            'status' => 'sometimes|integer|min:1|max:5',
            'tanggalDiambil' => 'sometimes|nullable|date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->userId,
            'nomor_ijazah' => $this->nomorIjazah,
            'jumlah_lembar' => $this->jumlahLembar,
            'tanggal_diambil' => $this->tanggalDiambil,
        ]);
    }
}