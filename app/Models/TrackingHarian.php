<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackingHarian extends Model
{
    use HasFactory;

    protected $table = 'tracking_harian';

    public $timestamps = false;

    protected $fillable = [
        'warga_id',
        'status_kesehatan',
        'status_lingkungan',
        'kategori_masalah',
        'deskripsi',
        'bukti_foto',
        'tanggal_pantau',
        'kader_id',
        'dibuat_pada',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function kader()
    {
        return $this->belongsTo(Kader::class);
    }
}
