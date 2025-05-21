<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'kelurahan';
    protected $fillable = ['kecamatan_id', 'nama_kelurahan'];

    // Relationships
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function rws()
    {
        return $this->hasMany(Rw::class);
    }

    public function rts()
    {
        return $this->hasMany(Rt::class);
    }

    public function wargas()
    {
        return $this->hasMany(Warga::class);
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}
