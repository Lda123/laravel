<?php

namespace Database\Seeders;

use App\Models\BukuPanduanKader;
use Illuminate\Database\Seeder;

class BukuPanduanSeeder extends Seeder
{
    public function run()
    {
        $books = [
            [
                'judul' => 'Buku Pedoman Pengendalian DBD di Indonesia',
                'penulis' => 'Tim Pengembangan Kader',
                'kelas' => 'Dasar',
                'tahun_terbit' => 2023,
                'deskripsi' => 'Panduan komprehensif untuk kader baru dengan materi dasar pengendalian DBD.',
                'file_pdf' => 'buku_panduan_kader.pdf', // Hanya nama file, tanpa path
                'cover_image' => 'buku_panduan_kader.png', // Hanya nama file, tanpa path
                'halaman' => 120,
                'created_at' => now()
            ],
            [
                'judul' => 'Petunjuk Teknis Implementasi PSN 3M-Plus',
                'penulis' => 'Tim Pengembangan Kader',
                'kelas' => 'Dasar',
                'tahun_terbit' => 2016,
                'deskripsi' => 'Panduan teknis pelaksanaan Pemberantasan Sarang Nyamuk dengan metode 3M-Plus.',
                'file_pdf' => 'buku_panduan_kader2.pdf',
                'cover_image' => 'buku_panduan_kader2.png',
                'halaman' => 56,
                'created_at' => now()
            ],
            [
                'judul' => 'Buku Saku Pengendalian DBD untuk Pengelolaan Program DBD di Puskesmas',
                'penulis' => 'Tim Pengembangan Kader',
                'kelas' => 'Dasar',
                'tahun_terbit' => 2013,
                'deskripsi' => 'Panduan praktis untuk petugas puskesmas dalam pengelolaan program DBD.',
                'file_pdf' => 'buku_panduan_kader3.pdf',
                'cover_image' => 'buku_panduan_kader3.png',
                'halaman' => 120,
                'created_at' => now()
            ],
        ];

        foreach ($books as $book) {
            BukuPanduanKader::create($book);
        }
    }
}