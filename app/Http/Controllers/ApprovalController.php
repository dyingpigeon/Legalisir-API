<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use setasign\Fpdi\Fpdi;
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
    // public function verifiedByOperator(Request $request, $id)
    // {
    //     $request->validate([
    //         'keterangan' => 'nullable|string|max:500'
    //     ]);

    //     $permohonan = Permohonan::findOrFail($id);

    //     // Cek authorization
    //     if (Auth::user()->role !== 'operator') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized. Hanya operator yang dapat melakukan verifikasi ini.'
    //         ], 403);
    //     }

    //     // Validasi status
    //     if ($permohonan->status !== Permohonan::STATUS_DIMULAI) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Permohonan tidak dapat diverifikasi. Status harus "Diterima".'
    //         ], 400);
    //     }

    //     return DB::transaction(function () use ($permohonan, $request) {
    //         // Update status permohonan
    //         $permohonan->update([
    //             'status' => Permohonan::STATUS_VERIFIKASI
    //         ]);

    //         // Simpan riwayat
    //         RiwayatStatus::create([
    //             'permohonan_id' => $permohonan->id,
    //             'user_id' => Auth::id(),
    //             'status_sebelum' => Permohonan::STATUS_DIMULAI,
    //             'status_sesudah' => Permohonan::STATUS_VERIFIKASI,
    //             'keterangan' => $request->keterangan ?? 'Diterima oleh Operator'
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Permohonan berhasil diterima',
    //             'data' => [
    //                 'permohonan' => $permohonan->load('user', 'riwayatStatus'),
    //                 'status_sebelum' => 'Dimulai',
    //                 'status_sesudah' => 'Diterima',
    //                 'updated_by' => Auth::user()->name
    //             ]
    //         ]);
    //     });
    // }
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

            $originalFilePath = storage_path('app/public/template_ijazah/' . $permohonan->nomor_ijazah . '.pdf');

            if (!file_exists($originalFilePath)) {
                throw new \Exception('File PDF tidak ditemukan: ' . $originalFilePath);
            }

            $pathInfo = pathinfo($permohonan->file);
            $stampedFilename = $pathInfo['filename'] . '_verified_' . time() . '.' . $pathInfo['extension'];
            $stampedFilePath = storage_path('app/public/Ijazah_Pemohon/' . $stampedFilename);

            // Tambahkan cap ke PDF
            $this->addVerificationStamp($originalFilePath, $stampedFilePath);

            // Simpan path file baru ke database
            $originalFile = $permohonan->file_ijazah;
            $permohonan->file_ijazah_verified = 'Ijazah_Pemohon/' . $stampedFilename;

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
        $allowedRoles = ['operator', 'user']; // Sesuaikan dengan kebutuhan

        if (!in_array(Auth::user()->role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya operator dan user yang dapat menandai sudah diambil.'
            ], 403);
        }


        // Validasi status
        if ($permohonan->status !== Permohonan::STATUS_SIAP_DIAMBIL) {
            return response()->json([
                'success' => false,
                'message' => 'Permohonan tidak dapat ditandai selesai. Status harus "Ditandatangani" terlebih dahulu.'
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

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan_pembatalan' => 'required|string|max:500'
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Cek authorization
        if (Auth::user()->role !== 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        // Tidak bisa menolak permohonan yang sudah siap diambil atau sudah diambil
        if ($permohonan->status === Permohonan::STATUS_SIAP_DIAMBIL) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membatalkan permohonan yang sudah siap diambil.'
            ], 400);
        }

        return DB::transaction(function () use ($permohonan, $request) {
            $statusSebelum = $permohonan->status;

            // Update status permohonan
            $permohonan->update([
                'status' => Permohonan::DIBATALKAN
            ]);

            // Simpan riwayat
            RiwayatStatus::create([
                'permohonan_id' => $permohonan->id,
                'user_id' => Auth::id(),
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => Permohonan::DIBATALKAN,
                'keterangan' => $request->alasan_pembatalan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan ditolak',
                'data' => [
                    'permohonan' => $permohonan->load('user', 'riwayatStatus'),
                    'status_sebelum' => $permohonan->getStatusLabel($statusSebelum),
                    'status_sesudah' => 'Ditolak',
                    'alasan_pembatalan' => $request->alasan_pembatalan,
                    'updated_by' => Auth::user()->name
                ]
            ]);
        });
    }

    /**
     * Get riwayat approval untuk permohonan tertentu
     */
    // public function getRiwayat($permohonanId)
    // {
    //     $permohonan = Permohonan::findOrFail($permohonanId);

    //     $riwayat = RiwayatStatus::with([
    //         'user' => function ($query) {
    //             $query->select('id', 'name', 'role');
    //         }
    //     ])
    //         ->where('permohonan_id', $permohonanId)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'permohonan' => $permohonan,
    //             'riwayat' => $riwayat
    //         ]
    //     ]);
    // }

    // /**
    //  * Get permohonan berdasarkan status dan role user
    //  */
    // public function getPermohonanByStatus(Request $request)
    // {
    //     $user = Auth::user();
    //     $status = $request->get('status');

    //     $query = Permohonan::with([
    //         'user' => function ($query) {
    //             $query->select('id', 'name', 'email');
    //         }
    //     ]);

    //     // Filter berdasarkan role
    //     switch ($user->role) {
    //         case 'operator':
    //             // Operator bisa melihat semua status kecuali yang baru dimulai
    //             $query->where('status', '>=', Permohonan::STATUS_DIMULAI);
    //             break;

    //         case 'wadir1':
    //             // Wadir hanya melihat status verifikasi dan yang sudah ditandatangani
    //             $query->whereIn('status', [
    //                 Permohonan::STATUS_VERIFIKASI,
    //                 Permohonan::STATUS_DITANDATANGANI
    //             ]);
    //             break;

    //         case 'user':
    //             // User hanya melihat permohonan mereka sendiri
    //             $query->where('user_id', $user->id);
    //             break;

    //         case 'superadmin':
    //             // Superadmin bisa melihat semua
    //             break;
    //     }

    //     // Filter by status jika ada
    //     if ($status) {
    //         $query->where('status', $status);
    //     }

    //     $permohonans = $query->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $permohonans
    //     ]);
    // }

    private function addVerificationStamp($sourcePath, $destinationPath)
    {
        // Konfigurasi mPDF
        $config = [
            'mode' => 'utf-8',
            'format' => [210, 297], // A4 portrait
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'tempDir' => storage_path('app/tmp/')
        ];

        $mpdf = new Mpdf($config);

        // Import halaman dari PDF asli
        // Import halaman dari PDF asli
        $pageCount = $mpdf->SetSourceFile($sourcePath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $mpdf->ImportPage($i);
            $size = $mpdf->getTemplateSize($templateId);

            // Debug: Lihat struktur array $size
            // \Log::info('Size array: ' . print_r($size, true));

            // Tentukan orientasi berdasarkan key yang ada
            // Key bisa berupa: 'width'/'height' atau 'w'/'h'
            $width = isset($size['width']) ? $size['width'] : (isset($size['w']) ? $size['w'] : 210);
            $height = isset($size['height']) ? $size['height'] : (isset($size['h']) ? $size['h'] : 297);
            $orientation = isset($size['orientation']) ? $size['orientation'] : null;

            // Tambah halaman
            if ($orientation) {
                // Jika orientasi sudah tersedia
                $mpdf->AddPage($orientation);
            } else {
                // Tentukan orientasi berdasarkan width dan height
                if ($width > $height) {
                    $mpdf->AddPage('L'); // Landscape
                } else {
                    $mpdf->AddPage('P'); // Portrait
                }
            }

            // Gunakan template halaman asli
            $mpdf->UseTemplate($templateId);

            // Tambahkan stamp verifikasi - gunakan width yang benar
            $this->drawMpdfVerificationStamp($mpdf, ['width' => $width, 'height' => $height]);
        }

        // Output ke file
        $mpdf->Output($destinationPath, \Mpdf\Output\Destination::FILE);
    }

    private function drawMpdfVerificationStamp($mpdf, $size)
    {
        // Posisi di atas kanan (margin 10mm dari tepi)
        $margin = 10;
        $stampWidth = 80; // Lebar cap dalam mm
        $stampHeight = 50; // Tinggi cap dalam mm

        // Koordinat X (kanan - margin - width)
        $x = $size['width'] - $stampWidth - $margin;
        // Koordinat Y (atas + margin)
        $y = $margin;

        // Warna resmi (biru instansi)
        $primaryColor = [0, 82, 155]; // RGB: #00529B
        $lightBlue = [230, 240, 250, 0.3]; // Warna biru muda dengan transparansi

        // 1. Gambar latar belakang cap (rounded rectangle) dengan transparansi
        $mpdf->SetFillColor($lightBlue[0], $lightBlue[1], $lightBlue[2]);
        $mpdf->SetAlpha($lightBlue[3]);
        $mpdf->RoundedRect($x, $y, $stampWidth, $stampHeight, 3, '1111', 'F');
        $mpdf->SetAlpha(1); // Reset transparansi

        // 2. Border cap
        $mpdf->SetLineWidth(0.5);
        $mpdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $mpdf->RoundedRect($x, $y, $stampWidth, $stampHeight, 3, '1111', 'D');

        // 3. Garis pemisah header
        $mpdf->SetLineWidth(0.3);
        $mpdf->Line($x, $y + 12, $x + $stampWidth, $y + 12);

        // 4. Text: "SUDAH DIVERIFIKASI"
        $mpdf->SetFont('helvetica', 'B', 9);
        $mpdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $mpdf->SetXY($x, $y + 2);
        $mpdf->Cell($stampWidth, 5, 'SUDAH DIVERIFIKASI', 0, 0, 'C');

        // 5. Text: Tanggal verifikasi
        $mpdf->SetFont('helvetica', '', 7);
        $mpdf->SetTextColor(100, 100, 100); // Abu-abu
        $mpdf->SetXY($x, $y + 7);
        $mpdf->Cell($stampWidth, 5, date('d/m/Y H:i'), 0, 0, 'C');

        // 6. Text: "TANDA TANGAN"
        $mpdf->SetFont('helvetica', 'B', 8);
        $mpdf->SetTextColor(0, 0, 0); // Hitam
        $mpdf->SetXY($x, $y + 15);
        $mpdf->Cell($stampWidth, 5, 'TANDA TANGAN', 0, 0, 'C');

        // 7. Text: "Wakil Direktur"
        $mpdf->SetFont('helvetica', '', 7);
        $mpdf->SetXY($x, $y + 20);
        $mpdf->Cell($stampWidth, 5, 'Wakil Direktur', 0, 0, 'C');

        // 8. Box untuk tanda tangan (garis putus-putus)
        $mpdf->SetLineWidth(0.2);
        $mpdf->SetDrawColor(150, 150, 150); // Abu-abu muda

        // Gambar box dengan garis putus-putus
        $this->drawMpdfDashedRect($mpdf, $x + 5, $y + 27, $stampWidth - 10, 12);

        // 9. Text di dalam box
        $mpdf->SetFont('helvetica', 'I', 6);
        $mpdf->SetTextColor(100, 100, 100);
        $mpdf->SetXY($x, $y + 31);
        $mpdf->Cell($stampWidth, 5, '(Tempat Tanda Tangan)', 0, 0, 'C');

        // 10. Nama pejabat
        $mpdf->SetFont('helvetica', 'B', 7);
        $mpdf->SetTextColor(0, 0, 0);
        $mpdf->SetXY($x, $y + 40);
        $mpdf->Cell($stampWidth, 5, 'Dr. H. Ahmad Wijaya, M.Pd.', 0, 0, 'C');

        // 11. NIP/NIDN (jika ada)
        $mpdf->SetFont('helvetica', '', 6);
        $mpdf->SetTextColor(100, 100, 100);
        $mpdf->SetXY($x, $y + 43);
        $mpdf->Cell($stampWidth, 5, 'NIP. 19651231 199203 1 001', 0, 0, 'C');
    }

    private function drawMpdfDashedRect($mpdf, $x, $y, $width, $height)
    {
        $dashLength = 2;
        $spaceLength = 2;

        // Garis atas
        $this->drawMpdfDashedLine($mpdf, $x, $y, $x + $width, $y);
        // Garis kanan
        $this->drawMpdfDashedLine($mpdf, $x + $width, $y, $x + $width, $y + $height);
        // Garis bawah
        $this->drawMpdfDashedLine($mpdf, $x, $y + $height, $x + $width, $y + $height);
        // Garis kiri
        $this->drawMpdfDashedLine($mpdf, $x, $y, $x, $y + $height);
    }

    private function drawMpdfDashedLine($mpdf, $x1, $y1, $x2, $y2)
    {
        // Untuk garis putus-putus di mPDF, gunakan pola dash
        $mpdf->SetLineWidth(0.2);

        // mPDF mendukung pola dash langsung
        $mpdf->Line($x1, $y1, $x2, $y2, [
            'dash' => '2,2', // Pattern: 2mm dash, 2mm space
            'cap' => 'butt',
            'join' => 'miter'
        ]);
    }


    /**
     * Fungsi untuk menggambar rectangle dengan garis putus-putus
     */
    private function drawDashedRect($pdf, $x, $y, $width, $height)
    {
        $dashLength = 2; // Panjang dash
        $spaceLength = 2; // Panjang space

        // Garis atas
        $this->drawDashedLine($pdf, $x, $y, $x + $width, $y, $dashLength, $spaceLength);

        // Garis kanan
        $this->drawDashedLine($pdf, $x + $width, $y, $x + $width, $y + $height, $dashLength, $spaceLength);

        // Garis bawah
        $this->drawDashedLine($pdf, $x, $y + $height, $x + $width, $y + $height, $dashLength, $spaceLength);

        // Garis kiri
        $this->drawDashedLine($pdf, $x, $y, $x, $y + $height, $dashLength, $spaceLength);
    }

    /**
     * Fungsi untuk menggambar garis putus-putus
     */
    private function drawDashedLine($pdf, $x1, $y1, $x2, $y2, $dashLength = 2, $spaceLength = 2)
    {
        $distance = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        $angle = atan2($y2 - $y1, $x2 - $x1);

        $currentPosition = 0;
        $drawing = true;

        while ($currentPosition < $distance) {
            $segmentLength = $drawing ? $dashLength : $spaceLength;

            if ($currentPosition + $segmentLength > $distance) {
                $segmentLength = $distance - $currentPosition;
            }

            $xStart = $x1 + cos($angle) * $currentPosition;
            $yStart = $y1 + sin($angle) * $currentPosition;
            $xEnd = $x1 + cos($angle) * ($currentPosition + $segmentLength);
            $yEnd = $y1 + sin($angle) * ($currentPosition + $segmentLength);

            if ($drawing) {
                $pdf->Line($xStart, $yStart, $xEnd, $yEnd);
            }

            $currentPosition += $segmentLength;
            $drawing = !$drawing;
        }
    }
}
