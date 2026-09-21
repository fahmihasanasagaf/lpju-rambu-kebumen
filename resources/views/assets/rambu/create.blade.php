@extends('layouts.app')
@section('title','Tambah Rambu')
@section('page-title','Tambah Rambu')
@section('content')<div class="mb-7"><p class="text-sm text-slate-600">Catat aset baru ke inventaris</p><h2 class="mt-1 text-2xl font-semibold text-navy-900">Tambah Rambu</h2></div>@include('assets.rambu._form',['editing'=>false])@endsection
