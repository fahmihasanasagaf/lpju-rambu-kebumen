<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Histori;
use App\Models\Rambu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $data['petugas_id'] = $request->user()->id;
        $data['qr_code'] = 'RAMBU-'.str_pad((string) ((Rambu::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto/rambu', 'public');
        }

        $rambu = Rambu::create($data);

        return response()->json(
            $rambu->load(['desa', 'sumberDana', 'petugas']),
            201
        );
    }

    public function show(string $id)
    {
        return response()->json(
            Rambu::with([
                'desa',
                'sumberDana',
                'petugas',
                'aduan',
                'histori',
            ])->findOrFail($id)
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'sometimes|in:baik,rusak,perbaikan',
            'keterangan_histori' => 'nullable|string|max:255',
        ]);

        $statusLama = $rambu->status;
        $keteranganHistori = $data['keterangan_histori'] ?? null;
        unset($data['keterangan_histori']);

        DB::transaction(function () use ($request, $rambu, $data, $statusLama, $keteranganHistori) {
            $oldFoto = $rambu->foto;
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('foto/rambu', 'public');
            }

            $rambu->update($data);

            if ($statusLama !== $rambu->status) {
                Histori::create([
                    'aset_type' => Rambu::class,
                    'aset_id' => $rambu->id,
                    'status_lama' => $statusLama,
                    'status_baru' => $rambu->status,
                    'keterangan' => $keteranganHistori,
                    'diubah_oleh' => $request->user()->id,
                    'tanggal' => now(),
                ]);
            }

            if ($request->hasFile('foto') && $oldFoto) {
                Storage::disk('public')->delete($oldFoto);
            }
        });

        return response()->json(
            $rambu->load(['desa', 'sumberDana', 'petugas'])
        );
    }

    public function destroy(string $id)
    {
        Rambu::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data Rambu berhasil dihapus',
        ]);
    }
}