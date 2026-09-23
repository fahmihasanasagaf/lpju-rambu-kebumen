<?php

use App\Http\Controllers\Api\AduanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DesaController;
use App\Http\Controllers\Api\HistoriController;
use App\Http\Controllers\Api\KecamatanController;
use App\Http\Controllers\Api\LpjuController;
use App\Http\Controllers\Api\RambuController;
use App\Http\Controllers\Api\SumberDanaController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Endpoint umum
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin,operator'])->group(function () {
    Route::apiResource('kecamatan', KecamatanController::class)->only(['index', 'show']);
    Route::apiResource('desa', DesaController::class)->only(['index', 'show']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('kecamatan', KecamatanController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('desa', DesaController::class)->only(['store', 'update', 'destroy']);
});

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Endpoint publik
|--------------------------------------------------------------------------
*/

Route::apiResource('sumber-dana', SumberDanaController::class)
    ->only(['index', 'show']);

Route::apiResource('lpju', LpjuController::class)
    ->only(['index', 'show']);

Route::apiResource('rambu', RambuController::class)
    ->only(['index', 'show']);

Route::apiResource('aduan', AduanController::class)
    ->only(['store'])
    ->middleware('throttle:aduan');

Route::middleware(['auth:sanctum', 'role:admin,operator'])->group(function () {
    Route::apiResource('histori', HistoriController::class)
        ->only(['index', 'show']);
});

/*
|--------------------------------------------------------------------------
| Endpoint Admin dan Operator
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin,operator'])->group(function () {
    Route::get('/aduan/summary', [AduanController::class, 'summary']);

    Route::apiResource('sumber-dana', SumberDanaController::class)
        ->only(['store', 'update']);

    Route::apiResource('lpju', LpjuController::class)
        ->only(['store', 'update']);

    Route::apiResource('rambu', RambuController::class)
        ->only(['store', 'update']);

    Route::apiResource('aduan', AduanController::class)
        ->only(['index', 'show', 'update']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::get('/users/{id}', [UserManagementController::class, 'show']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Endpoint khusus Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('sumber-dana', SumberDanaController::class)
        ->only(['destroy']);

    Route::apiResource('lpju', LpjuController::class)
        ->only(['destroy']);

    Route::apiResource('rambu', RambuController::class)
        ->only(['destroy']);

    Route::apiResource('aduan', AduanController::class)
        ->only(['destroy']);
});