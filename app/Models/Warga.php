<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Warga extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'warga';
    protected $fillable = [
        'nik',
        'nama_lengkap', 
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat_lengkap',
        'rt_id',
        'telepon',
        'password',
        'foto_ktp',
        'foto_diri_ktp'
    ];

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

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function keluhanHarians()
    {
        return $this->hasMany(KeluhanHarian::class, 'id_warga');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'warga_id');
    }

    public function events()
    {
        return $this->belongsToMany(ListEvent::class, 'event_warga', 'id_warga', 'id_event');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'warga_id');
    }
// Accessor untuk profile picture
public function getProfilePicAttribute($value)
{
    return $value ?: 'assets/img/default-profile.jpg';
}

}