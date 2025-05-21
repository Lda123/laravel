<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluhanHarian extends Model
{
    use HasFactory;

    protected $table = 'keluhan_harian';
    protected $fillable = [
        'id_warga',
        'tanggal',
        'suhu',
        'ruam',
        'nyeri_otot',
        'mual',
        'nyeri_belakang_mata',
        'pendarahan',
        'gejala_lain',
        'akurasi_dbd',
        'anjuran'
    ];

    // Relationships
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'id_warga');
    }
}