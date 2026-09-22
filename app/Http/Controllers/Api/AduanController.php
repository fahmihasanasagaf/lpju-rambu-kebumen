<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\Lpju;
use App\Models\Rambu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AduanController extends Controller
{
    public function index()
    {
        return response()->json(Aduan::with(['aset', 'penindak'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_aset' => ['required', Rule::in(['lpju', 'rambu'])],
            'aset_id' => 'required|integer',
            'nama_pelapor' => 'required|string|max:255',
            'kontak_pelapor' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal_aduan' => 'sometimes|date',
        ]);

        $modelClass = $data['jenis_aset'] === 'lpju' ? Lpju::class : Rambu::class;
        $modelClass::findOrFail($data['aset_id']);

        $foto = $request->hasFile('foto')
            ? $request->file('foto')->store('foto/aduan', 'public')
            : null;

        $aduan = Aduan::create([
            'aset_type' => $modelClass,
            'aset_id' => $data['aset_id'],
            'nama_pelapor' => $data['nama_pelapor'],
            'kontak_pelapor' => $data['kontak_pelapor'] ?? null,
            'deskripsi' => $data['deskripsi'],
            'foto' => $foto,
            'status_aduan' => 'baru',
            'tanggal_aduan' => $data['tanggal_aduan'] ?? now(),
        ]);

        return response()->json($aduan->load('aset'), 201);
    }

    public function show(string $id)
    {
        return response()->json(Aduan::with(['aset', 'penindak'])->findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $aduan = Aduan::findOrFail($id);

        $data = $request->validate([
            'status_aduan' => ['sometimes', Rule::in(['baru', 'diproses', 'selesai'])],
        ]);

        $data['ditindak_oleh'] = $request->user()->id;

        $aduan->update($data);

        return response()->json($aduan->load(['aset', 'penindak']));
    }

    public function destroy(string $id)
    {
        Aduan::findOrFail($id)->delete();

        return response()->json(['message' => 'Aduan berhasil dihapus']);
    }
}