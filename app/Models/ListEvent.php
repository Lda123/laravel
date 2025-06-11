<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function kaders()
    {
        return $this->belongsToMany(Kader::class, 'event_kader', 'id_event', 'id_kader')
                    ->withTimestamps();
    }
    public function eventKader()
    {
        return $this->hasMany(EventKader::class, 'id_event', 'id');
    }

    public function scopeForKader($query)
    {
        return $query->where('kategori_pengguna', 'kader');
    }

    /**
     * Scope untuk event khusus warga
     */
    public function scopeForWarga($query)
    {
        return $query->where('kategori_pengguna', 'warga');
    }

     public function scopeUpcoming($query)
    {
        return $query->where('tanggal', '>=', Carbon::today());
    }

    /**
     * Scope untuk event yang sudah lewat
     */
    public function scopePast($query)
    {
        return $query->where('tanggal', '<', Carbon::today());
    }

    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getTanggalFormatAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->format('d F Y') : null;
    }

    /**
     * Cek apakah event sudah lewat
     */
    public function isPast()
    {
        return $this->tanggal && Carbon::parse($this->tanggal)->isPast();
    }

    /**
     * Cek apakah event akan datang
     */
    public function isUpcoming()
    {
        return $this->tanggal && Carbon::parse($this->tanggal)->isFuture();
    }

    /**
     * Hitung jumlah peserta yang terdaftar
     */
    public function getJumlahPesertaAttribute()
    {
        return $this->kaders()->count();
    }
}