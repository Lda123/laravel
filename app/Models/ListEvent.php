<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListEvent extends Model
{
    use HasFactory;

    protected $table = 'list_event';
    protected $fillable = [
        'nama_event',
        'tanggal',
        'lokasi',
        'waktu',
        'biaya',
        'kategori_pengguna'
    ];

    // Relationships
    public function wargas()
    {
        return $this->belongsToMany(Warga::class, 'event_warga', 'id_event', 'id_warga');
    }
}