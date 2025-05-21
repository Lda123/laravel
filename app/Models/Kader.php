<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Kader extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'kader';
    protected $fillable = ['nama_lengkap', 'telepon', 'password', 'rt_id'];
    
    protected $dates = ['dibuat_pada'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    // Relationships
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'kader_id');
    }
}