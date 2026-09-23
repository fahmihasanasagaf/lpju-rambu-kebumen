<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lpju extends Model
{
    protected $table = 'lpju';

    protected $fillable = [
        'desa_id',
        'sumber_dana_id',
        'alamat',
        'latitude',
        'longitude',
        'foto',
        'qr_code',
        'tahun_anggaran',
        'tanggal_diterima',
        'tanggal_pasang',
        'status',
        'petugas_id',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class, 'sumber_dana_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
        public function aduan()
    {
        return $this->morphMany(Aduan::class, 'aset');
    }

    public function histori()
    {
        return $this->morphMany(Histori::class, 'aset');
    }
}