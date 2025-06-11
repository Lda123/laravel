<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuPanduanKader extends Model
{
    use HasFactory;

    protected $table = 'buku_panduan_kader';

    public $timestamps = false; // Karena hanya ada created_at

    protected $fillable = [
        'judul',
        'penulis',
        'kelas',
        'tahun_terbit',
        'deskripsi',
        'file_pdf',
        'cover_image',
        'created_at',
    ];

    // Di model BukuPanduanKader
public function getCoverImageAttribute($value)
{
    if (!$value) return null;
    return 'bukupanduan/covers/' . $value;
}
}