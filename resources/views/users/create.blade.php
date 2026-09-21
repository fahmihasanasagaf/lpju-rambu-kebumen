@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('page-title','Tambah Pengguna')
@section('content')<div class="mb-7"><p class="text-sm text-slate-600">Akun internal untuk petugas terotorisasi</p><h2 class="mt-1 text-2xl font-semibold text-navy-900">Tambah pengguna</h2></div>@include('users._form',['editing'=>false])@endsection
