<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';

    protected $primaryKey = 'id';

    protected $fillable = ['kode', 'kecamatan', 'latitude', 'longitude'];

    public function desa()
    {
        return $this->hasMany(Desa::class, 'kd_kec', 'kode');
    }
}