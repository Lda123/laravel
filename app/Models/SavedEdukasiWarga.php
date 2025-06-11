<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedEdukasiWarga extends Model
{
    use HasFactory;

    protected $table = 'saved_edukasi_warga';
    
    protected $fillable = [
        'warga_id',
        'edukasi_id',
        'saved_at'
    ];

    protected $casts = [
        'saved_at' => 'datetime'
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function edukasi()
    {
        return $this->belongsTo(Edukasi::class);
    }

    // Scope for edukasi that still exists
    public function scopeWithExistingEdukasi($query)
    {
        return $query->whereHas('edukasi');
    }
}