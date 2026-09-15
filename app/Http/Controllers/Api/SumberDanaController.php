<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SumberDana;
use Illuminate\Http\Request;

class SumberDanaController extends Controller
{
    public function index()
    {
        return response()->json(SumberDana::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_sumber' => 'required|string|max:255',
        ]);

        $sumberDana = SumberDana::create($data);

        return response()->json($sumberDana, 201);
    }

    public function show(string $id)
    {
        return response()->json(SumberDana::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $sumberDana = SumberDana::findOrFail($id);

        $data = $request->validate([
            'nama_sumber' => 'sometimes|string|max:255',
        ]);

        $sumberDana->update($data);

        return response()->json($sumberDana);
    }

    public function destroy(string $id)
    {
        SumberDana::findOrFail($id)->delete();

        return response()->json(['message' => 'Sumber dana berhasil dihapus']);
    }
}