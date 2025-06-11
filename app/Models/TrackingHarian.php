<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class TrackingHarian extends Model
{
    use HasFactory;
    
    protected $table = 'tracking_harian';
    public $timestamps = true;
    
    protected $fillable = [
        'warga_id',
        'warga_nik',
        'nama_warga',
        'kader_id',
        'tanggal',
        'kategori_masalah',
        'deskripsi',
        'bukti_foto',
        'status',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected $appends = ['foto_url', 'status_text', 'tanggal_formatted'];
    
    // Accessor untuk URL foto
    public function getFotoUrlAttribute()
    {
        if (!$this->bukti_foto) {
            return null;
        }
        
        $fotoPath = $this->bukti_foto;
        
        // Jika path sudah lengkap (dimulai dengan http)
        if (str_starts_with($fotoPath, 'http')) {
            return $fotoPath;
        }
        // Jika path dimulai dengan storage/
        elseif (str_starts_with($fotoPath, 'storage/')) {
            return asset($fotoPath);
        }
        // Jika path dimulai dengan tracking_harian/
        elseif (str_starts_with($fotoPath, 'tracking_harian/')) {
            return asset('storage/' . $fotoPath);
        }
        // Path lainnya
        else {
            return asset('storage/' . $fotoPath);
        }
    }
    
    // Accessor untuk konsistensi dengan view yang sudah ada
    public function getTanggalPantauAttribute()
    {
        return $this->tanggal;
    }
    
    // Accessor untuk status yang konsisten
    public function getStatusTextAttribute()
    {
        return $this->kategori_masalah ?? 'Belum Dicek';
    }
    
    // Override getAttribute untuk status agar konsisten
    public function getStatusAttribute($value)
    {
        // Jika ada nilai status, gunakan itu, jika tidak gunakan kategori_masalah
        return $value ?: ($this->kategori_masalah ?? 'Belum Dicek');
    }
    
    public function getKeteranganAttribute()
    {
        return $this->deskripsi ?? 'Tidak ada keterangan';
    }
    
    // Accessor untuk format tanggal yang user-friendly
    public function getTanggalFormattedAttribute()
    {
        if (!$this->tanggal) {
            return '-';
        }
        
        return Carbon::parse($this->tanggal)->format('d M Y');
    }
    
    // Accessor untuk format tanggal lengkap
    public function getTanggalFullAttribute()
    {
        if (!$this->tanggal) {
            return '-';
        }
        
        return Carbon::parse($this->tanggal)->format('l, d F Y');
    }
    
    // Method untuk mendapatkan badge class berdasarkan status
    public function getStatusBadgeClassAttribute()
    {
        $status = $this->kategori_masalah ?? 'Belum Dicek';
        
        switch($status) {
            case 'Aman':
                return 'bg-green-100 text-green-800';
            case 'Tidak Aman':
                return 'bg-red-100 text-red-800';
            case 'Belum Dicek':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
    
    // Method untuk mendapatkan icon berdasarkan status
    public function getStatusIconAttribute()
    {
        $status = $this->kategori_masalah ?? 'Belum Dicek';
        
        switch($status) {
            case 'Aman':
                return 'check-circle'; // Success icon
            case 'Tidak Aman':
                return 'x-circle'; // Error icon
            case 'Belum Dicek':
                return 'clock'; // Warning icon
            default:
                return 'question-mark-circle';
        }
    }
    
    // Relationships
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }
    
    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }
    
    // Scopes
    public function scopeForWarga($query, $wargaId)
    {
        return $query->where('warga_id', $wargaId);
    }
    
    public function scopeByKader($query, $kaderId)
    {
        return $query->where('kader_id', $kaderId);
    }
    
    public function scopeTidakAman($query)
    {
        return $query->where('kategori_masalah', 'Tidak Aman');
    }
    
    public function scopeAman($query)
    {
        return $query->where('kategori_masalah', 'Aman');
    }
    
    public function scopeBelumDicek($query)
    {
        return $query->where(function($q) {
            $q->where('kategori_masalah', 'Belum Dicek')
              ->orWhereNull('kategori_masalah');
        });
    }
    
    public function scopeByMonth($query, $month)
    {
        return $query->whereMonth('tanggal', $month);
    }
    
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal', $year);
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('kategori_masalah', $status);
    }
    
    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year);
    }
    
    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal', now()->year);
    }
    
    // Method untuk cek apakah ada masalah
    public function hasIssue()
    {
        return $this->kategori_masalah === 'Tidak Aman';
    }
    
    // Method untuk cek apakah status aman
    public function isSafe()
    {
        return $this->kategori_masalah === 'Aman';
    }
    
    // Method untuk cek apakah belum dicek
    public function isUnchecked()
    {
        return in_array($this->kategori_masalah, ['Belum Dicek', null]);
    }
    
    // Method untuk mendapatkan pesan status
    public function getStatusMessage()
    {
        switch($this->kategori_masalah) {
            case 'Aman':
                return 'Lingkungan rumah dalam kondisi aman dari risiko DBD';
            case 'Tidak Aman':
                return 'Terdapat indikasi risiko DBD di lingkungan rumah';
            case 'Belum Dicek':
            default:
                return 'Status lingkungan belum diperiksa';
        }
    }
    
    // Method untuk mendapatkan rekomendasi tindakan
    public function getActionRecommendation()
    {
        switch($this->kategori_masalah) {
            case 'Aman':
                return 'Tetap jaga kebersihan lingkungan dan lakukan 3M Plus';
            case 'Tidak Aman':
                return 'Segera lakukan tindakan pencegahan dan hubungi kader kesehatan';
            case 'Belum Dicek':
            default:
                return 'Hubungi kader kesehatan untuk melakukan pemeriksaan';
        }
    }
}