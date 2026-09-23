<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AduanHistory;
use App\Models\Histori;
use Illuminate\Http\Request;

class HistoriController extends Controller
{
    public function index(Request $request)
    {
        $assetRows = Histori::with(['aset', 'pengubah'])->get()->map(function (Histori $history) {
            $type = str_contains(strtolower($history->aset_type), 'rambu') ? 'rambu' : 'lpju';
            return array_merge($history->toArray(), ['source' => $type, 'code' => strtoupper($type).'-'.str_pad((string) $history->aset_id, 4, '0', STR_PAD_LEFT)]);
        });
        $complaintRows = AduanHistory::with(['aduan.aset', 'pengubah'])->get()->map(function (AduanHistory $history) {
            return array_merge($history->toArray(), [
                'source' => 'aduan',
                'aduan_id' => $history->aduan_id,
                'code' => 'ADUAN-'.str_pad((string) $history->aduan_id, 6, '0', STR_PAD_LEFT),
                'tanggal' => $history->created_at,
                'pengubah' => $history->pengubah,
                'aduan' => $history->aduan,
            ]);
        });
        $items = $assetRows->concat($complaintRows)->sortByDesc(fn ($row) => $row['tanggal'] ?? $row['created_at'] ?? null)->values();
        if ($request->filled('source')) $items = $items->where('source', $request->input('source'))->values();
        if ($request->filled('status_baru')) $items = $items->where('status_baru', $request->input('status_baru'))->values();
        return response()->json($items);
    }

    public function show(string $id)
    {
        return response()->json(Histori::with(['aset', 'pengubah'])->findOrFail($id));
    }
}
