<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Approve permohonan oleh operator (status 1 -> 2)
     */
    public function verifiedByOperator(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        if (Auth::user()->role !== 'operator') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya operator yang dapat melakukan verifikasi ini.'
            ], 403);
        }

        // Validasi status
        if ($permohonan->status !== Permohonan::STATUS_DIMULAI) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak dapat diverifikasi. Status harus "Diterima".'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::STATUS_VERIFIKASI
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => Permohonan::STATUS_DIMULAI,
                'status_sesudah' => Permohonan::STATUS_VERIFIKASI,
                'keterangan' => $request->keterangan ?? 'Diterima oleh Operator'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil diterima',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => 'Dimulai',
                    'status_sesudah' => 'Diterima',
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * tandatangan oleh wadir1 (status 2 -> 3)
     */
    public function signedByWadir(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        if (Auth::user()->role !== 'wadir1') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya wadir yang dapat melakukan ttd ini.'
            ], 403);
        }

        // Validasi status
        if ($permohonan->status !== Permohonan::STATUS_VERIFIKASI) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak dapat ditandatangan. Status harus "Diverifikasi" oleh operator terlebih dahulu.'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::STATUS_DITANDATANGANI
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => Permohonan::STATUS_VERIFIKASI,
                'status_sesudah' => Permohonan::STATUS_DITANDATANGANI,
                'keterangan' => $request->keterangan ?? 'DiTTD oleh Wadir'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berhasil diverifikasi',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => 'Diterima',
                    'status_sesudah' => 'Verifikasi',
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * Update status ke ditandatangani (status 3 -> 4)
     */
    // public function markAsSigned(Request $request, $id)
    // {
    //     $request->validate([
    //         'keterangan' => 'nullable|string|max:500'
    //     ]);

    //     $permohonan = Permohonan::findOrFail($id);

    //     // Cek role yang diizinkan
    //     if (!in_array(Auth::user()->role, ['operator', 'wadir1'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized.'
    //         ], 403);
    //     }

    //     // Validasi status
    //     if ($permohonan->status !== Permohonan::STATUS_VERIFIKASI) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Permohonan tidak dapat ditandatangani. Status harus "Verifikasi" terlebih dahulu.'
    //         ], 400);
    //     }

    //     return DB::transaction(function () use ($permohonan, $request) {
    //         // Update status permohonan
    //         $permohonan->update([
    //             'status' => Permohonan::STATUS_DITANDATANGANI
    //         ]);

    //         // Simpan riwayat
    //         RiwayatStatus::create([
    //             'permohonan_id' => $permohonan->id,
    //             'user_id' => Auth::id(),
    //             'status_sebelum' => Permohonan::STATUS_VERIFIKASI,
    //             'status_sesudah' => Permohonan::STATUS_DITANDATANGANI,
    //             'keterangan' => $request->keterangan ?? 'Telah Ditandatangani'
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Permohonan berhasil ditandatangani',
    //             'data' => [
    //                 'permohonan' => $permohonan->load('user', 'riwayatStatus'),
    //                 'status_sebelum' => 'Verifikasi',
    //                 'status_sesudah' => 'Ditandatangani',
    //                 'updated_by' => Auth::user()->name
    //             ]
    //         ]);
    //     });
    // }

    /**
     * Update status ke siap diambil (status 3 -> 4)
     */
    public function markAsReady(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        if (Auth::user()->role !== 'operator') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya operator yang dapat menandai siap diambil.'
            ], 403);
        }

        // Validasi status
        if ($permohonan->status !== Permohonan::STATUS_DITANDATANGANI) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak dapat ditandai siap diambil. Status harus "Ditandatangani" terlebih dahulu.'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::STATUS_SIAP_DIAMBIL
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => Permohonan::STATUS_DITANDATANGANI,
                'status_sesudah' => Permohonan::STATUS_SIAP_DIAMBIL,
                'keterangan' => $request->keterangan ?? 'Siap Diambil'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan siap diambil',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => 'Ditandatangani',
                    'status_sesudah' => 'Siap Diambil',
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * Permohonan sudah diambil
     */

    public function markAsDone(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        $allowedRoles = ['operator', 'admin']; // Sesuaikan dengan kebutuhan

        if (!in_array(Auth::user()->role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya operator yang dapat menandai sudah diambil.'
            ], 403);
        }


        // Validasi status
        if ($permohonan->status !== Permohonan::STATUS_SIAP_DIAMBIL) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak dapat ditandai siap diambil. Status harus "Ditandatangani" terlebih dahulu.'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::STATUS_SUDAH_DIAMBIL
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => Permohonan::STATUS_SIAP_DIAMBIL,
                'status_sesudah' => Permohonan::STATUS_SUDAH_DIAMBIL,
                'keterangan' => $request->keterangan ?? 'sudah Diambil'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan sudah diambil',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => 'Ditandatangani',
                    'status_sesudah' => 'Siap Diambil',
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * Tolak permohonan (status apapun -> 6)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        if (!in_array(Auth::user()->role, ['operator', 'wadir1'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        // Tidak bisa menolak permohonan yang sudah siap diambil atau sudah diambil
        if ($permohonan->status === Permohonan::STATUS_SIAP_DIAMBIL) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menolak permohonan yang sudah siap diambil.'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            $statusSebelum = $permohonan->status;

            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::DITOLAK
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => Permohonan::DITOLAK,
                'keterangan' => $request->alasan_penolakan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan ditolak',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => $permohonan->getStatusLabel($statusSebelum),
                    'status_sesudah' => 'Ditolak',
                    'alasan_penolakan' => $request->alasan_penolakan,
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * Get riwayat approval untuk permohonan tertentu
     */
    public function getRiwayat($permohonanId)
    {
        $permohonan = Permohonan::findOrFail($permohonanId);

        $riwayat = RiwayatStatus::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'role');
            }
        ])
            ->where('permohonan_id', $permohonanId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'permohonan' => $permohonan,
                'riwayat' => $riwayat
            ]
        ]);
    }

    /**
     * Get permohonan berdasarkan status dan role user
     */
    public function getPermohonanByStatus(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');

        $query = Permohonan::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'email');
            }
        ]);

        // Filter berdasarkan role
        switch ($user->role) {
            case 'operator':
                // Operator bisa melihat semua status kecuali yang baru dimulai
                $query->where('status', '>=', Permohonan::STATUS_DIMULAI);
                break;

            case 'wadir1':
                // Wadir hanya melihat status verifikasi dan yang sudah ditandatangani
                $query->whereIn('status', [
                    Permohonan::STATUS_VERIFIKASI,
                    Permohonan::STATUS_DITANDATANGANI
                ]);
                break;

            case 'user':
                // User hanya melihat permohonan mereka sendiri
                $query->where('user_id', $user->id);
                break;

            case 'superadmin':
                // Superadmin bisa melihat semua
                break;
        }

        // Filter by status jika ada
        if ($status) {
            $query->where('status', $status);
        }

        $permohonans = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $permohonans
        ]);
    }
}