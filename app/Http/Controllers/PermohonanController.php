<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermohonanRequest;
use App\Http\Resources\PermohonanResource;
use App\Http\Requests\UpdatePermohonanRequest;

class PermohonanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permohonan::query();
        $query = Permohonan::with(['user']);

        // Filter untuk pencarian (gunakan LIKE)
        if ($request->has('keperluan')) {
            $query->where('keperluan', 'like', '%' . $request->keperluan . '%');
        }

        if ($request->has('file')) {
            $query->where('file', 'like', '%' . $request->file . '%');
        }

        // Filter untuk exact match (gunakan = bukan LIKE)
        if ($request->has('user')) {
            $query->where('user_id', $request->user);  // Exact match
        }

        if ($request->has('username')) {
            $query->where('username', $request->username);  // Exact match
        }

        if ($request->has('nomor_ijazah')) {
            $query->where('nomor_ijazah', $request->nomor_ijazah);  // Exact match
        }

        if ($request->has('jumlah_lembar')) {
            $query->where('jumlah_lembar', $request->jumlah_lembar);  // Exact match
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);  // Exact match
        }

        // Filter berdasarkan tanggal
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        if ($request->has('tanggal_diambil')) {
            $query->whereDate('tanggal_diambil', $request->tanggal_diambil);
        }

        return \App\Http\Resources\PermohonanResource::collection($query->paginate(10));
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
    public function store(StorePermohonanRequest $request)
    {
        return new PermohonanResource(Permohonan::create($request->all()));
    }

    public function buatPermohonan(UpdatePermohonanRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['username'] = Auth::user()->username;
        $data['status'] = 1;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $timestamp = time();
            $data2 = Auth::user()->username;

            $filename = 'Permohonan_milik_' . $data2 . '_Nomor_ijazah_' . $data['nomor_ijazah'] . '_' . $timestamp . '.' . $extension;

            $file->storeAs('Ijazah_Pemohon', $filename, 'public');

            // Simpan ke kedua kolom jika perlu
            $data['file_url'] = $filename;
            $data['file'] = $filename; // atau $data['file'] = $filename;

            // JANGAN di-unset
            // unset($data['file']);
        }

        $permohonan = Permohonan::create($data);
        return new PermohonanResource($permohonan);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permohonan $permohonan)
    {
        $permohonan->load(['user']);
        return new PermohonanResource($permohonan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermohonanRequest $request, Permohonan $permohonan)
    {
        $permohonan->update($request->all());
        return new PermohonanResource($permohonan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permohonan = Permohonan::findOrFail($id);
        $permohonan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan berhasil dihapus',
        ], 200);
    }
}