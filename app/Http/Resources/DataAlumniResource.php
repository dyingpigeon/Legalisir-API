<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataAlumniResource extends JsonResource
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
            'nim' => $this->nim,
            'email' => $this->email,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'jk' => $this->jk,
            'namaIbu' => $this->nama_Ibu,
            'agama' => $this->agama,
            'tempatLahir' => $this->tempat_lahir,
            'tanggalLahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'hp' => $this->hp,
            'nomorIjazahElektronik' => $this->nomor_Ijazah_Elektronik,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}