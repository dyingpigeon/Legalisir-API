<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatStatusResource extends JsonResource
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
            'permohonanId' => $this->permohonan_id,
            'permohonanData' => $this->permohonan ? [
                'nomorIjazah' => $this->permohonan->nomor_ijazah,
                'username' => $this->permohonan->username,
            ] : null,
            'userId' => $this->user_id,
            'userName' => $this->user->name ?? null,
            'statusSebelum' => $this->status_sebelum,
            'statusSebelumText' => $this->getStatusText($this->status_sebelum),
            'statusSesudah' => $this->status_sesudah,
            'statusSesudahText' => $this->getStatusText($this->status_sesudah),
            'keterangan' => $this->keterangan,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    /**
     * Get status text based on status code
     */
    private function getStatusText(?int $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $statusMap = [
            1 => 'Dimulai',
            2 => 'Diterima',
            3 => 'Verifikasi',
            4 => 'Ditandatangani',
            5 => 'Siap Diambil'
        ];

        return $statusMap[$status] ?? 'Tidak Diketahui';
    }
}