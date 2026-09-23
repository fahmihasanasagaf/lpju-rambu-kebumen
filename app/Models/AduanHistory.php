<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AduanHistory extends Model
{
    protected $fillable = ['aduan_id', 'status_lama', 'status_baru', 'keterangan', 'diubah_oleh'];

    public function aduan(): BelongsTo { return $this->belongsTo(Aduan::class); }
    public function pengubah(): BelongsTo { return $this->belongsTo(User::class, 'diubah_oleh'); }
}
