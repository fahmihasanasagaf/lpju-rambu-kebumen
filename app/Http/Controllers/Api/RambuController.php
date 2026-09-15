<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rambu;
use Illuminate\Http\Request;

class RambuController extends Controller
{
    public function index()
    {
        return response()->json(
            Rambu::with(['desa', 'sumberDana', 'petugas'])->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'desa_id' => 'required|integer|exists:desa,id',
            'sumber_dana_id' => 'required|integer|exists:sumber_dana,id',
            'jenis_rambu' => 'required|string|max:255',
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

        $rambu = Rambu::create($data);

        return response()->json($rambu->load(['desa', 'sumberDana', 'petugas']), 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Rambu::with(['desa', 'sumberDana', 'petugas', 'aduan', 'histori'])->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $rambu = Rambu::findOrFail($id);

        $data = $request->validate([
            'desa_id' => 'sometimes|integer|exists:desa,id',
            'sumber_dana_id' => 'sometimes|integer|exists:sumber_dana,id',
            'jenis_rambu' => 'sometimes|string|max:255',
            'alamat' => 'sometimes|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'foto' => 'nullable|string',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $rambu->update($data);

        return response()->json($rambu->load(['desa', 'sumberDana', 'petugas']));
    }

    public function destroy(string $id)
    {
        Rambu::findOrFail($id)->delete();

        return response()->json(['message' => 'Data Rambu berhasil dihapus']);
    }
}