<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuPanduanKader extends Model
{
    use HasFactory;

    protected $table = 'PanduanKader';

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
}