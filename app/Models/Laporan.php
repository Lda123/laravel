<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $fillable = [
        'warga_id',
        'alamat_detail',
        'rt_id',
        'rw_id',
        'kelurahan_id',
        'kecamatan_id',
        'jenis_laporan',
        'deskripsi',
        'status',
        'foto_pelaporan'
    ];

    protected $dates = ['dibuat_pada'];

    const STATUS_PENDING = 'Pending';
    const STATUS_VERIFIED = 'Terverifikasi';
    const STATUS_COMPLETED = 'Selesai';

    const JENIS_JENTIK = 'Jentik Nyamuk';
    const JENIS_DBD = 'Kasus DBD';
    const JENIS_LINGKUNGAN = 'Lingkungan Kotor';

    // Relationships
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}