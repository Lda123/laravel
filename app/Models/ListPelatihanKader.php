<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListPelatihanKader extends Model
{
    use HasFactory;

    protected $table = 'listpelatihan';

    protected $fillable = [
        'nama_pelatihan',
        'tanggal',
        'lokasi',
        'waktu',
        'biaya',
    ];

    // Relasi: pelatihan memiliki banyak kader yang ikut
    public function kader()
    {
        return $this->belongsToMany(Kader::class, 'PelatihanKader', 'id_pelatihan', 'id_kader');
    }
}
