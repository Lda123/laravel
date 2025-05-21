<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    protected $fillable = ['nama_kecamatan'];

    // Relationships
    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
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