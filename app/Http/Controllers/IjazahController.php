<?php

namespace App\Http\Controllers;

use App\Models\Ijazah;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIjazahRequest;
use App\Http\Resources\IjazahResource;
use App\Http\Requests\UpdateIjazahRequest;

class IjazahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ijazah::query();

        // Filter untuk exact match
        if ($request->has('nomor_ijazah')) {
            $query->where('nomor_ijazah', $request->nomor_ijazah);
        }

        if ($request->has('nim')) {
            $query->where('nim', $request->nim);
        }

        // Filter untuk pencarian (gunakan LIKE)
        if ($request->has('path_file')) {
            $query->where('path_file', 'like', '%' . $request->path_file . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        return IjazahResource::collection($query->paginate(10));
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
    public function store(StoreIjazahRequest $request)
    {
        return new IjazahResource(Ijazah::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ijazah $ijazah)
    {
        return new IjazahResource($ijazah);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIjazahRequest $request, Ijazah $ijazah)
    {
        $ijazah->update($request->all());
        return new IjazahResource($ijazah);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ijazah = Ijazah::findOrFail($id);
        $ijazah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ijazah berhasil dihapus',
        ], 200);
    }
}