<?php
// app/Models/LaporanBulanan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    use HasFactory;

    protected $table = 'laporan_bulanan';

    protected $fillable = [
        'kader_id',
        'nama_file',
        'path_file',
        'nama_asli_file',
        'ukuran_file',
        'tanggal_upload',
    ];

    protected $dates = ['tanggal_upload'];

    // Cast untuk data types
    protected $casts = [
        'tanggal_upload' => 'datetime',
        'ukuran_file' => 'integer',
    ];

    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }

    // Accessor untuk menampilkan ukuran file dalam format yang mudah dibaca
    public function getUkuranFileFormatAttribute()
    {
        if (!$this->ukuran_file) {
            return '-';
        }

        $size = $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    // Accessor untuk URL download file
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->path_file);
    }

    // Scope untuk filter berdasarkan kader
    public function scopeByKader($query, $kaderId)
    {
        return $query->where('kader_id', $kaderId);
    }

    // Scope untuk filter berdasarkan bulan/tahun
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal_upload', $month)
                    ->whereYear('tanggal_upload', $year);
    }
}