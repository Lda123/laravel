<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_post';
    
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

    public function author()
    {
        if ($this->kader_id) {
            return $this->belongsTo(Kader::class, 'kader_id');
        }
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function kader()
    {
        return $this->belongsTo(Kader::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumPost::class, 'parent_id')->with(['kader', 'warga']);
    }

    public function getAuthorNameAttribute()
    {
        if ($this->kader) {
            return $this->kader->nama_lengkap;
        }
        if ($this->warga) {
            return $this->warga->nama_lengkap;
        }
        return 'Anonymous';
    }

    public function getAuthorInitialsAttribute()
    {
        $name = $this->author_name;
        if ($name !== 'Anonymous') {
            return substr($name, 0, 2);
        }
        return 'AN';
    }
}