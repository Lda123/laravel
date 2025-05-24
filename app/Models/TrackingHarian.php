<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackingHarian extends Model
{
    use HasFactory;

    protected $table = 'tracking_harian';

    // Aktifkan timestamps karena tabel memiliki created_at dan updated_at
    public $timestamps = true;

    protected $fillable = [
        'warga_id',
        'warga_nik',
        'nama_warga',
        'kader_id',
        'tanggal',
        'keterangan',
        'kategori_masalah',
        'deskripsi',
        'bukti_foto',
        'status',
        'dibuat_pada',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dibuat_pada' => 'datetime',
    ];

    /**
     * Relasi ke model Warga
     */
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    /**
     * Relasi ke model Kader
     */
    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }
}
