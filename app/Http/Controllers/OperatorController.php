<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOperatorRequest;
use App\Http\Resources\OperatorResource;
use App\Http\Requests\UpdateOperatorRequest;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Operator::query();

        // Filter untuk exact match
        if ($request->has('nip')) {
            $query->where('nip', $request->nip);
        }

        if ($request->has('nik')) {
            $query->where('nik', $request->nik);
        }

        // Filter untuk pencarian (gunakan LIKE)
        if ($request->has('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if ($request->has('jk')) {
            $query->where('jk', $request->jk);
        }

        // Filter berdasarkan tanggal
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        return OperatorResource::collection($query->paginate(10));
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
    public function store(StoreOperatorRequest $request)
    {
        return new OperatorResource(Operator::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Operator $operator)
    {
        return new OperatorResource($operator);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperatorRequest $request, Operator $operator)
    {
        $operator->update($request->all());
        return new OperatorResource($operator);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $operator = Operator::findOrFail($id);
        $operator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Operator berhasil dihapus',
        ], 200);
    }
}