<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiwayatStatusRequest extends FormRequest
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
                'permohonanId' => ['required', 'integer', 'exists:permohonans,id'],
                'userId' => ['required', 'integer', 'exists:users,id'],
                'statusSebelum' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5'],
                'statusSesudah' => ['required', 'integer', 'min:1', 'max:5'],
                'keterangan' => ['sometimes', 'nullable', 'string'],
            ];
        } else {
            return [
                'permohonanId' => ['sometimes', 'integer', 'exists:permohonans,id'],
                'userId' => ['sometimes', 'integer', 'exists:users,id'],
                'statusSebelum' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5'],
                'statusSesudah' => ['sometimes', 'integer', 'min:1', 'max:5'],
                'keterangan' => ['sometimes', 'nullable', 'string'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        if ($this->has('permohonanId')) {
            $this->merge(['permohonan_id' => $this->permohonanId]);
        }
        if ($this->has('userId')) {
            $this->merge(['user_id' => $this->userId]);
        }
        if ($this->has('statusSebelum')) {
            $this->merge(['status_sebelum' => $this->statusSebelum]);
        }
        if ($this->has('statusSesudah')) {
            $this->merge(['status_sesudah' => $this->statusSesudah]);
        }
    }
}