<?php

// Model EventKader
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventKader extends Model
{
    use HasFactory;

    protected $table = 'event_kader';

    protected $fillable = [
        'id_kader',
        'id_event',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke model Kader
     */
    public function kader()
    {
        return $this->belongsTo(Kader::class, 'id_kader', 'id');
    }

    /**
     * Relasi ke model ListEvent
     */
    public function event()
    {
        return $this->belongsTo(ListEvent::class, 'id_event', 'id');
    }
}
