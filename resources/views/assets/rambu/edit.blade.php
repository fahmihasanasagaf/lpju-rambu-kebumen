@extends('layouts.app')
@section('title','Edit Rambu')
@section('page-title','Edit Rambu')
@section('content')<div class="mb-7"><p class="text-sm text-slate-600">Perbarui data dan histori aset</p><h2 class="mt-1 text-2xl font-semibold text-navy-900">Edit Rambu #{{ $id }}</h2></div>@include('assets.rambu._form',['editing'=>true,'id'=>$id])@endsection
