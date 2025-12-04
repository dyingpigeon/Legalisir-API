<?php

namespace App\Http\Controllers;

use App\Models\DataAlumni;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDataAlumniRequest;
use App\Http\Resources\DataAlumniResource;
use App\Http\Requests\UpdateDataAlumniRequest;

class DataAlumniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DataAlumni::query();

        // Filter untuk exact match
        if ($request->has('nim')) {
            $query->where('nim', $request->nim);
        }

        if ($request->has('nik')) {
            $query->where('nik', $request->nik);
        }

        if ($request->has('email')) {
            $query->where('email', $request->email);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter untuk pencarian (gunakan LIKE)
        if ($request->has('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if ($request->has('nama_Ibu')) {
            $query->where('nama_Ibu', 'like', '%' . $request->nama_Ibu . '%');
        }

        if ($request->has('agama')) {
            $query->where('agama', 'like', '%' . $request->agama . '%');
        }

        if ($request->has('tempat_lahir')) {
            $query->where('tempat_lahir', 'like', '%' . $request->tempat_lahir . '%');
        }

        if ($request->has('nomor_Ijazah_Elektronik')) {
            $query->where('nomor_Ijazah_Elektronik', 'like', '%' . $request->nomor_Ijazah_Elektronik . '%');
        }

        // Filter enum
        if ($request->has('jk')) {
            $query->where('jk', $request->jk);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_lahir')) {
            $query->whereDate('tanggal_lahir', $request->tanggal_lahir);
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        return DataAlumniResource::collection($query->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataAlumniRequest $request)
    {
        return new DataAlumniResource(DataAlumni::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    // public function show(DataAlumni $dataAlumni)
    // {
    //     return new DataAlumniResource($dataAlumni);
    // }

    public function show($id)
    {
        $dataAlumni = DataAlumni::find($id);

        if (!$dataAlumni) {
            return response()->json([
                'success' => false,
                'message' => 'Data alumni tidak ditemukan'
            ], 404);
        }

        return new DataAlumniResource($dataAlumni);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateDataAlumniRequest $request, DataAlumni $dataAlumni)
    // {
    //     $dataAlumni->update($request->all());
    //     return new DataAlumniResource($dataAlumni);
    // }

    public function update(UpdateDataAlumniRequest $request, $id)
    {
        $dataAlumni = DataAlumni::find($id);

        if (!$dataAlumni) {
            return response()->json([
                'success' => false,
                'message' => 'Data alumni tidak ditemukan'
            ], 404);
        }

        $dataAlumni->update($request->all());
        return new DataAlumniResource($dataAlumni);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dataAlumni = DataAlumni::findOrFail($id);
        $dataAlumni->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data alumni berhasil dihapus',
        ], 200);
    }

    /**
     * Search alumni by NIM and NIK (both must match the same record)
     */
    public function search(Request $request)
    {
        // Validasi
        $request->validate([
            'nim' => 'required',
            'nik' => 'required',
        ]);

        \Log::info('SEARCH CALLED - NIM: ' . $request->nim . ', NIK: ' . $request->nik);

        // Gunakan QUERY BUIDER yang SAMA PERSIS dengan method index()
        $query = DataAlumni::query();

        if ($request->has('nim')) {
            $query->where('nim', $request->nim);
        }

        if ($request->has('nik')) {
            $query->where('nik', $request->nik);
        }

        $dataAlumni = $query->first();

        \Log::info('SEARCH RESULT: ' . ($dataAlumni ? 'FOUND' : 'NOT FOUND'));

        if (!$dataAlumni) {
            return response()->json([
                'success' => false,
                'message' => 'Data alumni tidak ditemukan dengan NIM dan NIK yang sesuai'
            ], 404);
        }

        return new DataAlumniResource($dataAlumni);
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(Request $request)
    {
        $query = DataAlumni::query();

        // Search by NIM (exact match)
        if ($request->has('nim')) {
            $query->where('nim', $request->nim);
        }

        // Search by NIK (exact match)
        if ($request->has('nik')) {
            $query->where('nik', $request->nik);
        }

        // Search by name (partial match)
        if ($request->has('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Search by email (exact match)
        if ($request->has('email')) {
            $query->where('email', $request->email);
        }

        $results = $query->paginate($request->get('per_page', 10));

        return DataAlumniResource::collection($results);
    }

    // Tambahkan method debug di controller
    public function debugSearch(Request $request)
    {
        $nim = $request->nim;
        $nik = $request->nik;

        // Ambil data pertama dari database untuk referensi
        $firstRecord = DataAlumni::first();

        // Test 1: Query seperti di index() yang berhasil
        $test1 = DataAlumni::where('nim', $request->nim)
            ->where('nik', $request->nik)
            ->first();

        // Test 2: Query dengan casting
        $test2 = DataAlumni::where('nim', (int) $nim)
            ->where('nik', (int) $nik)
            ->first();

        // Test 3: Query dengan value dari database record pertama
        $test3 = null;
        if ($firstRecord) {
            $test3 = DataAlumni::where('nim', $firstRecord->nim)
                ->where('nik', $firstRecord->nik)
                ->first();
        }

        return response()->json([
            'input' => [
                'nim' => $nim,
                'nik' => $nik
            ],
            'database_first_record' => $firstRecord ? [
                'id' => $firstRecord->id,
                'nim' => $firstRecord->nim,
                'nik' => $firstRecord->nik,
            ] : null,
            'test_results' => [
                'test_1_like_index' => $test1 ? 'FOUND - ID: ' . $test1->id : 'NOT FOUND',
                'test_2_with_casting' => $test2 ? 'FOUND - ID: ' . $test2->id : 'NOT FOUND',
                'test_3_with_db_values' => $test3 ? 'FOUND - ID: ' . $test3->id : 'NOT FOUND',
            ],
            'individual_checks' => [
                'nim_exists' => DataAlumni::where('nim', $nim)->exists(),
                'nik_exists' => DataAlumni::where('nik', $nik)->exists(),
            ]
        ]);
    }


    public function testLog()
    {
        \Log::emergency('EMERGENCY test message');
        \Log::alert('ALERT test message');
        \Log::critical('CRITICAL test message');
        \Log::error('ERROR test message');
        \Log::warning('WARNING test message');
        \Log::notice('NOTICE test message');
        \Log::info('INFO test message');
        \Log::debug('DEBUG test message');

        return response()->json([
            'message' => 'Log messages sent. Check storage/logs/laravel.log'
        ]);
    }
}