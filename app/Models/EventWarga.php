<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventWarga extends Model
{
    use HasFactory;

    protected $table = 'event_warga';
    protected $fillable = ['id_warga', 'id_event'];

    // Relationships
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'id_warga');
    }

    public function event()
    {
        return $this->belongsTo(ListEvent::class, 'id_event');
    }
}