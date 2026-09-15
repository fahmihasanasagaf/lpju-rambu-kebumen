<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Histori;

class HistoriController extends Controller
{
    public function index()
    {
        return response()->json(Histori::with(['aset', 'pengubah'])->get());
    }

    public function show(string $id)
    {
        return response()->json(Histori::with(['aset', 'pengubah'])->findOrFail($id));
    }
}