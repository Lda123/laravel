<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveVideoKaders extends Model
{
    use HasFactory;

    protected $table = 'savedvideos';

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kader_id',
        'video_id',
        'saved_at',
    ];

    protected $dates = ['saved_at'];

    // Relasi ke Kader
    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }

    // Relasi ke Video
    public function video()
    {
        return $this->belongsTo(Edukasi::class, 'video_id');
    }
}