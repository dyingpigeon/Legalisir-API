<?php

namespace App\Http\Controllers;

use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRiwayatStatusRequest;
use App\Http\Resources\RiwayatStatusResource;
use App\Http\Requests\UpdateRiwayatStatusRequest;

class RiwayatStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RiwayatStatus::query();
        $query = RiwayatStatus::with(['permohonan', 'user']);

        // Filter untuk exact match
        if ($request->has('permohonan_id')) {
            $query->where('permohonan_id', $request->permohonan_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status_sebelum')) {
            $query->where('status_sebelum', $request->status_sebelum);
        }

        if ($request->has('status_sesudah')) {
            $query->where('status_sesudah', $request->status_sesudah);
        }

        // Filter untuk pencarian (gunakan LIKE)
        if ($request->has('keterangan')) {
            $query->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        return RiwayatStatusResource::collection($query->paginate(10));
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
    public function store(StoreRiwayatStatusRequest $request)
    {
        return new RiwayatStatusResource(RiwayatStatus::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatStatus $riwayatStatus)
    {
        $riwayatStatus->load(['permohonan', 'user']);
        return new RiwayatStatusResource($riwayatStatus);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRiwayatStatusRequest $request, RiwayatStatus $riwayatStatus)
    {
        $riwayatStatus->update($request->all());
        return new RiwayatStatusResource($riwayatStatus);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $riwayatStatus = RiwayatStatus::findOrFail($id);
        $riwayatStatus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status berhasil dihapus',
        ], 200);
    }
}