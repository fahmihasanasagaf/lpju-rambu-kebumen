@extends('layouts.app')
@section('title', 'Tambah LPJU')
@section('page-title', 'Tambah LPJU')
@section('content')
<div class="mb-7"><p class="text-sm text-slate-600">Catat aset baru ke inventaris</p><h2 class="mt-1 text-2xl font-semibold tracking-tight text-navy-900">Tambah LPJU</h2></div>
@include('assets.lpju._form', ['editing' => false])
@endsection
