<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    use HasFactory;

    protected $table = 'edukasi';
    protected $fillable = [
        'judul', 
        'isi', 
        'tipe', 
        'kategori_pengguna', 
        'tautan',
        'thumbnail_url',
        'durasi',
        'kategori',
        'views',
        'created_at',
        'updated_at'
    ];
    
    const TIPE_VIDEO = 'Video';
    const TIPE_ARTIKEL = 'Artikel';

    const KATEGORI_WARGA = 'Warga';
    const KATEGORI_KADER = 'Kader';

    // Scope for filtering videos
    public function scopeVideo($query)
    {
        return $query->where('tipe', self::TIPE_VIDEO);
    }

    // Scope for filtering by user category
    public function scopeForKader($query)
    {
        return $query->where('kategori_pengguna', self::KATEGORI_KADER);
    }

    // Dalam App\Models\Edukasi
    public function scopeForWarga($query)
    {
        return $query->where('kategori_pengguna', self::KATEGORI_WARGA); // Sebelumnya salah menggunakan KATEGORI_KADER
    }

    public function savedByKaders()
    {
        return $this->belongsToMany(
            Kader::class, 
            'saved_videos', 
            'video_id', 
            'kader_id'
        )->withTimestamps()
         ->withPivot('saved_at');
    }

    // In App\Models\Edukasi

    public function savedByWargas()
    {
        return $this->belongsToMany(
            Warga::class, 
            'saved_edukasi_warga', 
            'edukasi_id', 
            'warga_id'
        )->withTimestamps()
        ->withPivot('saved_at');
    }

    public function savedEdukasiWargaRecords()
    {
        return $this->hasMany(SavedEdukasiWarga::class, 'edukasi_id');
    }

    public function savedVideoRecords()
    {
        return $this->hasMany(SavedVideo::class, 'video_id');
    }
}