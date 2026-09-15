<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lpju;
use Illuminate\Http\Request;

class LpjuController extends Controller
{
    public function index()
    {
        return response()->json(
            Lpju::with(['desa', 'sumberDana', 'petugas'])->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'desa_id' => 'required|integer|exists:desa,id',
            'sumber_dana_id' => 'required|integer|exists:sumber_dana,id',
            'alamat' => 'required|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'foto' => 'nullable|string',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $data['petugas_id'] = $request->user()->id;

        $lpju = Lpju::create($data);

        return response()->json($lpju->load(['desa', 'sumberDana', 'petugas']), 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Lpju::with(['desa', 'sumberDana', 'petugas', 'aduan', 'histori'])->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $lpju = Lpju::findOrFail($id);

        $data = $request->validate([
            'desa_id' => 'sometimes|integer|exists:desa,id',
            'sumber_dana_id' => 'sometimes|integer|exists:sumber_dana,id',
            'alamat' => 'sometimes|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'foto' => 'nullable|string',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $lpju->update($data);

        return response()->json($lpju->load(['desa', 'sumberDana', 'petugas']));
    }

    public function destroy(string $id)
    {
        Lpju::findOrFail($id)->delete();

        return response()->json(['message' => 'Data LPJU berhasil dihapus']);
    }
}