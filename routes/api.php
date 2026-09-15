<?php

use App\Http\Controllers\Api\DesaController;
use App\Http\Controllers\Api\KecamatanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AduanController;
use App\Http\Controllers\Api\HistoriController;
use App\Http\Controllers\Api\LpjuController;
use App\Http\Controllers\Api\RambuController;
use App\Http\Controllers\Api\SumberDanaController;    

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('kecamatan', KecamatanController::class);
Route::apiResource('desa', DesaController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
// Endpoint publik (boleh diakses tanpa login)
Route::apiResource('sumber-dana', SumberDanaController::class)->only(['index', 'show']);
Route::apiResource('lpju', LpjuController::class)->only(['index', 'show']);
Route::apiResource('rambu', RambuController::class)->only(['index', 'show']);
Route::apiResource('aduan', AduanController::class)->only(['store']);
Route::apiResource('histori', HistoriController::class)->only(['index', 'show']);

// Endpoint yang butuh login (Admin/Operator)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('sumber-dana', SumberDanaController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('lpju', LpjuController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('rambu', RambuController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('aduan', AduanController::class)->only(['index', 'show', 'update', 'destroy']);
    });
    