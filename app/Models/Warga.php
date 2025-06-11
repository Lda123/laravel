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

    public function trackingHarians()
    {
        return $this->hasMany(TrackingHarian::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class, 'warga_id');
    }

    // Method untuk relasi ke tracking harian terbaru
    public function latestStatus()
    {
        return $this->hasOne(TrackingHarian::class)->latest();
    }

    // Accessor untuk mendapatkan status terbaru sebagai string
    public function getLatestStatusTextAttribute()
    {
        $latestTracking = $this->trackingHarian()->latest()->first();
        return $latestTracking ? $latestTracking->status : 'Belum ada status';
    }

    // Method untuk mendapatkan tracking harian terbaru
    public function getLatestTrackingAttribute()
    {
        return $this->trackingHarian()->latest()->first();
    }

    // Accessor untuk profile picture
    public function getProfilePicAttribute($value)
    {
        return $value ?: '/images/default-profile.jpg';
    }
    // In App\Models\Warga

    public function savedEdukasi()
    {
        return $this->belongsToMany(
            Edukasi::class,
            'saved_edukasi_warga',
            'warga_id',
            'edukasi_id'
        )->withTimestamps()
        ->withPivot('saved_at');
    }
}