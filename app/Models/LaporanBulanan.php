<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    use HasFactory;

    protected $table = 'laporan_bulanan';

    public $timestamps = false;

    protected $fillable = [
        'kader_id',
        'nama_file',
        'path_file',
        'tanggal_upload',
    ];

    protected $dates = ['tanggal_upload'];

    // Relasi ke Kader
    public function kader()
    {
        return $this->belongsTo(Kader::class, 'kader_id');
    }
}