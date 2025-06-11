<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_post';
    
    // Override Laravel's default timestamp column names
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';
    
    protected $fillable = [
        'warga_id',
        'kader_id',
        'parent_id',
        'topik',
        'pesan',
        'gambar'
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    // Relationships
    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function kader()
    {
        return $this->belongsTo(Kader::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }
}