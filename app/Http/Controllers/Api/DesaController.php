<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function index()
    {
        return response()->json(Desa::with('kecamatan')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kd_kec' => 'required|string|max:2|exists:kecamatan,kode',
            'kd_desa' => 'nullable|string|max:4',
            'desa' => 'required|string|max:64',
            'kec_dtks' => 'nullable|string|max:32',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $desa = Desa::create($data);

        return response()->json($desa->load('kecamatan'), 201);
    }

    public function show(string $id)
    {
        $desa = Desa::with('kecamatan')->findOrFail($id);

        return response()->json($desa);
    }

    public function update(Request $request, string $id)
    {
        $desa = Desa::findOrFail($id);

        $data = $request->validate([
            'kd_kec' => 'sometimes|string|max:2|exists:kecamatan,kode',
            'kd_desa' => 'nullable|string|max:4',
            'desa' => 'sometimes|string|max:64',
            'kec_dtks' => 'nullable|string|max:32',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $desa->update($data);

        return response()->json($desa->load('kecamatan'));
    }

    public function destroy(string $id)
    {
        Desa::findOrFail($id)->delete();

        return response()->json(['message' => 'Desa berhasil dihapus']);
    }
}