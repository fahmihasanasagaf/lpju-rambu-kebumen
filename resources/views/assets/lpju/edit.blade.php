@extends('layouts.app')
@section('title', 'Edit LPJU')
@section('page-title', 'Edit LPJU')
@section('content')
<div class="mb-7"><p class="text-sm text-slate-600">Perbarui data aset dan histori perubahan</p><h2 class="mt-1 text-2xl font-semibold tracking-tight text-navy-900">Edit LPJU <span class="text-teal-600">#{{ $id }}</span></h2></div>
@include('assets.lpju._form', ['editing' => true, 'id' => $id])
@endsection
