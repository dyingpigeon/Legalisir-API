<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nip' => $this->nip,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'jk' => $this->jk,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}