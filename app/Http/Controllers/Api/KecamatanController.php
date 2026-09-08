<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function index()
    {
        return response()->json(Kecamatan::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:2',
            'kecamatan' => 'required|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $kecamatan = Kecamatan::create($data);

        return response()->json($kecamatan, 201);
    }

    public function show(string $id)
    {
        $kecamatan = Kecamatan::with('desa')->findOrFail($id);

        return response()->json($kecamatan);
    }

    public function update(Request $request, string $id)
    {
        $kecamatan = Kecamatan::findOrFail($id);

        $data = $request->validate([
            'kode' => 'sometimes|string|max:2',
            'kecamatan' => 'sometimes|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $kecamatan->update($data);

        return response()->json($kecamatan);
    }

    public function destroy(string $id)
    {
        Kecamatan::findOrFail($id)->delete();

        return response()->json(['message' => 'Kecamatan berhasil dihapus']);
    }
}