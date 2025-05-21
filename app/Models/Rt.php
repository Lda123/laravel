<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;

    protected $table = 'rt';
    protected $fillable = ['rw_id', 'kelurahan_id', 'nomor_rt', 'koordinat_lat', 'koordinat_lng'];

    // Relationships
    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function wargas()
    {
        return $this->hasMany(Warga::class);
    }

    public function kaders()
    {
        return $this->hasMany(Kader::class);
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}