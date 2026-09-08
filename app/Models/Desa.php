<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $table = 'desa';

    protected $primaryKey = 'id';

    protected $fillable = ['kd_kec', 'kd_desa', 'desa', 'kec_dtks', 'latitude', 'longitude'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kec', 'kode');
    }
}