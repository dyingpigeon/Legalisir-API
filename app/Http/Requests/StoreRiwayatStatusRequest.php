<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiwayatStatusRequest extends FormRequest
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
            'permohonanId' => 'required|integer|exists:permohonans,id',
            'userId' => 'required|integer|exists:users,id',
            'statusSebelum' => 'sometimes|nullable|integer|min:1|max:5',
            'statusSesudah' => 'required|integer|min:1|max:5',
            'keterangan' => 'sometimes|nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'permohonan_id' => $this->permohonanId,
            'user_id' => $this->userId,
            'status_sebelum' => $this->statusSebelum,
            'status_sesudah' => $this->statusSesudah,
        ]);
    }
}