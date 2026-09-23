<?php

use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/aduan', 'complaints.create')->name('complaints.create');
Route::view('/complaints', 'complaints.index')->name('complaints.index');
Route::view('/complaints/{id}', 'complaints.show')->name('complaints.show');
Route::view('/dashboard', 'dashboard.index')->name('dashboard');
Route::view('/assets/map', 'assets.map')->name('assets.map');
Route::view('/assets/lpju', 'assets.lpju.index')->name('assets.lpju.index');
Route::view('/assets/lpju/create', 'assets.lpju.create')->name('assets.lpju.create');
Route::view('/assets/lpju/{id}', 'assets.lpju.show')->name('assets.lpju.show');
Route::view('/assets/lpju/{id}/edit', 'assets.lpju.edit')->name('assets.lpju.edit');
Route::get('/assets/lpju/{id}/qr', [QrCodeController::class, 'lpju'])->name('assets.lpju.qr');
Route::get('/assets/rambu/{id}/qr', [QrCodeController::class, 'rambu'])->name('assets.rambu.qr');
Route::view('/assets/rambu', 'assets.rambu.index')->name('assets.rambu.index');
Route::view('/assets/rambu/create', 'assets.rambu.create')->name('assets.rambu.create');
Route::view('/assets/rambu/{id}', 'assets.rambu.show')->name('assets.rambu.show');
Route::view('/history', 'history.index')->name('history.index');
Route::view('/users', 'users.index')->name('users.index');
Route::view('/users/create', 'users.create')->name('users.create');
