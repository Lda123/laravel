<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedVideo extends Model
{
    use HasFactory;

    protected $table = 'saved_videos';
    
    protected $fillable = [
        'kader_id',
        'video_id',
        'saved_at'
    ];

    protected $casts = [
        'saved_at' => 'datetime'
    ];

    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }

    public function video()
    {
        return $this->belongsTo(Edukasi::class, 'video_id');
    }

    public function warga()
    {
        return $this->belongsTo(Kader::class, 'warga_id');
    }

    // Scope untuk video yang masih ada (tidak dihapus)
    public function scopeWithExistingVideo($query)
    {
        return $query->whereHas('video');
    }
}