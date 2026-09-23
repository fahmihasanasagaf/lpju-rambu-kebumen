<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\AduanHistory;
use App\Models\Lpju;
use App\Models\Rambu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AduanController extends Controller
{
    public function index(Request $request)
    {
        $query = Aduan::with(['aset', 'penindak'])->latest('tanggal_aduan')->latest('id');

        if ($request->filled('status_aduan')) $query->where('status_aduan', (string) $request->input('status_aduan'));
        if ($request->filled('jenis_aset')) $query->where('aset_type', $request->jenis_aset === 'rambu' ? Rambu::class : Lpju::class);
        if ($request->filled('from')) $query->whereDate('tanggal_aduan', '>=', $request->date('from'));
        if ($request->filled('to')) $query->whereDate('tanggal_aduan', '<=', $request->date('to'));
        if ($request->filled('search')) {
            $term = '%'.(string) $request->input('search').'%';
            $query->where(function ($builder) use ($term) {
                $builder->where('id', 'like', $term)
                    ->orWhere('alamat_kejadian', 'like', $term)
                    ->orWhere('deskripsi', 'like', $term);
            });
        }

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_aset' => ['required', Rule::in(['lpju', 'rambu'])],
            'aset_id' => ['required', 'integer'],
            'kategori_aduan' => ['required', 'string', 'max:100'],
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'kontak_pelapor' => ['nullable', 'string', 'max:255'],
            'alamat_kejadian' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tanggal_aduan' => ['sometimes', 'date'],
        ]);

        $modelClass = $data['jenis_aset'] === 'lpju' ? Lpju::class : Rambu::class;
        $modelClass::findOrFail($data['aset_id']);
        $foto = null;

        try {
            if ($request->hasFile('foto')) $foto = $request->file('foto')->store('foto/aduan', 'public');
            $aduan = DB::transaction(fn () => Aduan::create([
                'aset_type' => $modelClass,
                'aset_id' => $data['aset_id'],
                'nama_pelapor' => $data['nama_pelapor'],
                'kontak_pelapor' => $data['kontak_pelapor'] ?? null,
                'kategori_aduan' => $data['kategori_aduan'],
                'deskripsi' => $data['deskripsi'],
                'alamat_kejadian' => $data['alamat_kejadian'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'foto' => $foto,
                'status_aduan' => 'baru',
                'tanggal_aduan' => $data['tanggal_aduan'] ?? now()->toDateString(),
            ]));
        } catch (\Throwable $exception) {
            if ($foto) Storage::disk('public')->delete($foto);
            throw $exception;
        }

        return response()->json($aduan->load('aset'), 201);
    }

    public function summary()
    {
        $total = Aduan::count();
        $selesai = Aduan::where('status_aduan', 'selesai')->count();
        return response()->json(['total' => $total, 'selesai' => $selesai, 'persentase_selesai' => $total ? (int) round(($selesai / $total) * 100) : 0]);
    }

    public function show(string $id)
    {
        return response()->json(Aduan::with(['aset', 'penindak', 'histories.pengubah'])->findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $aduan = Aduan::findOrFail($id);
        $data = $request->validate([
            'status_aduan' => ['required', Rule::in(['baru', 'diproses', 'selesai'])],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
        $statusLama = $aduan->status_aduan;
        DB::transaction(function () use ($aduan, $data, $request, $statusLama) {
            $aduan->update(['status_aduan' => $data['status_aduan'], 'ditindak_oleh' => $request->user()->id]);
            if ($statusLama !== $aduan->status_aduan) {
                AduanHistory::create([
                    'aduan_id' => $aduan->id,
                    'status_lama' => $statusLama,
                    'status_baru' => $aduan->status_aduan,
                    'keterangan' => $data['keterangan'] ?? null,
                    'diubah_oleh' => $request->user()->id,
                ]);
            }
        });
        return response()->json($aduan->load(['aset', 'penindak', 'histories.pengubah']));
    }

    public function destroy(string $id)
    {
        $aduan = Aduan::findOrFail($id);
        if ($aduan->foto) Storage::disk('public')->delete($aduan->foto);
        $aduan->delete();
        return response()->json(['message' => 'Aduan berhasil dihapus']);
    }
}
