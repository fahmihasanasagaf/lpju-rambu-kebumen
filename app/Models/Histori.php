<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Histori extends Model
{
    protected $table = 'histori';

    protected $fillable = [
        'aset_type',
        'aset_id',
        'status_lama',
        'status_baru',
        'keterangan',
        'diubah_oleh',
        'tanggal',
    ];

    public function aset()
    {
        return $this->morphTo();
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}