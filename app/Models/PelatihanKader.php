<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelatihanKader extends Model
{
    protected $table = 'PelatihanKader';
    public $timestamps = false;

    protected $fillable = [
        'id_kader',
        'id_pelatihan',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(ListPelatihanKader::class, 'id_pelatihan');
    }

    public function kader()
    {
        return $this->belongsTo(Kader::class, 'id_kader');
    }
}
