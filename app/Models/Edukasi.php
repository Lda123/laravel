<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    use HasFactory;

    protected $table = 'edukasi';
    protected $fillable = ['judul', 'isi', 'tipe', 'kategori_pengguna', 'tautan'];
    
    protected $dates = ['dibuat_pada'];

    const TIPE_VIDEO = 'Video';
    const TIPE_ARTIKEL = 'Artikel';

    const KATEGORI_WARGA = 'Warga';
    const KATEGORI_KADER = 'Kader';
}