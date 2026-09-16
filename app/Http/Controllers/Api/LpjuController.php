<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Histori;
use App\Models\Lpju;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $data['petugas_id'] = $request->user()->id;

        $lpju = Lpju::create($data);

            if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto/lpju', 'public');
    }

            $lpju = Lpju::create($data);

            return response()->json(
                $lpju->load(['desa', 'sumberDana', 'petugas']),
                201
            );
        }

    public function show(string $id)
    {
        return response()->json(
            Lpju::with([
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
        $lpju = Lpju::findOrFail($id);

        $data = $request->validate([
            'desa_id' => 'sometimes|integer|exists:desa,id',
            'sumber_dana_id' => 'sometimes|integer|exists:sumber_dana,id',
            'alamat' => 'sometimes|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tahun_anggaran' => 'nullable|integer',
            'tanggal_diterima' => 'nullable|date',
            'tanggal_pasang' => 'nullable|date',
            'status' => 'sometimes|string',
            'keterangan_histori' => 'nullable|string|max:255',
        ]);

        // Simpan status sebelum diperbarui.
        $statusLama = $lpju->status;

        // Ambil keterangan histori, tetapi jangan dimasukkan
        // ke tabel lpju karena kolom ini hanya untuk tabel histori.
        $keteranganHistori = $data['keterangan_histori'] ?? null;
        unset($data['keterangan_histori']);

        $lpju->update($data);

        if ($request->hasFile('foto')) {
        if ($lpju->foto) {
            Storage::disk('public')->delete($lpju->foto);
        }

        $data['foto'] = $request->file('foto')->store('foto/lpju', 'public');
    } else {
        unset($data['foto']);
    }
    $lpju->update($data);

        // Buat histori hanya jika status berubah.
        if ($statusLama !== $lpju->status) {
            Histori::create([
                'aset_type' => Lpju::class,
                'aset_id' => $lpju->id,
                'status_lama' => $statusLama,
                'status_baru' => $lpju->status,
                'keterangan' => $keteranganHistori,
                'diubah_oleh' => $request->user()->id,
                'tanggal' => now(),
            ]);
        }

        return response()->json(
            $lpju->load(['desa', 'sumberDana', 'petugas'])
        );
    }

    public function destroy(string $id)
    {
        Lpju::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data LPJU berhasil dihapus',
        ]);
    }
}