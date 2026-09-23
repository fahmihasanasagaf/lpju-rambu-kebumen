<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    protected $table = 'aduan';

    protected $fillable = [
        'aset_type',
        'aset_id',
        'nama_pelapor',
        'kontak_pelapor',
        'kategori_aduan',
        'deskripsi',
        'alamat_kejadian',
        'latitude',
        'longitude',
        'foto',
        'status_aduan',
        'tanggal_aduan',
        'ditindak_oleh',
    ];

    public function aset()
    {
        return $this->morphTo();
    }

    public function penindak()
    {
        return $this->belongsTo(User::class, 'ditindak_oleh');
    }

    public function histories()
    {
        return $this->hasMany(AduanHistory::class)->latest();
    }
}