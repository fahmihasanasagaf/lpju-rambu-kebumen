<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    protected $table = 'sumber_dana';

    protected $fillable = ['nama_sumber'];

    public function lpju()
    {
        return $this->hasMany(Lpju::class);
    }

    public function rambu()
    {
        return $this->hasMany(Rambu::class);
    }
}