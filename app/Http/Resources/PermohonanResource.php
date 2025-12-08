<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanResource extends JsonResource
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
            'userId' => $this->user_id,
            'userName' => $this->user->name ?? null, // Tambahkan nama user
            'username' => $this->username,
            'nomorIjazah' => $this->nomor_ijazah,
            'jumlahLembar' => $this->jumlah_lembar,
            'keperluan' => $this->keperluan,
            'file' => $this->file,
            'file_url' => $this->file ? asset('storage/Ijazah_Pemohon/' . $this->file) : null,
            'status' => $this->status,
            'statusText' => $this->getStatusText(), // Tambahkan teks status
            'tanggalDiambil' => $this->tanggal_diambil,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    /**
     * Get status text based on status code
     */
    private function getStatusText(): string
    {
        $statusMap = [
            1 => 'Dimulai',
            2 => 'Verifikasi',
            3 => 'Ditandatangani',
            4 => 'Siap Diambil',
            5 => 'Sudah Diambil',
            6 => 'Ditolak'
        ];

        return $statusMap[$this->status] ?? 'Tidak Diketahui';
    }
}