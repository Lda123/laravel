<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EdukasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $videos = [
            [
                'judul' => 'Pencegahan DBD di Lingkungan Rumah',
                'isi' => 'Video ini membahas cara sederhana mencegah DBD dari rumah sendiri.',
                'tautan' => 'https://youtu.be/VwM6ruhcb78?si=sU7KWUfcSOBFCwj5',
            ],
            [
                'judul' => 'Kenali Gejala DBD Sejak Dini',
                'isi' => 'Pelajari gejala umum DBD agar dapat ditangani lebih cepat.',
                'tautan' => 'https://www.youtube.com/watch?v=fpHCtVshc9A',
            ],
            [
                'judul' => 'Cara Kerja Nyamuk Aedes Aegypti',
                'isi' => 'Penjelasan mengenai siklus hidup dan bahaya nyamuk penyebar DBD.',
                'tautan' => 'https://www.youtube.com/watch?v=8FiyN-CNFSc',
            ],
            [
                'judul' => 'Langkah 3M Plus Cegah DBD',
                'isi' => 'Penjelasan metode 3M Plus dalam memberantas DBD.',
                'tautan' => 'https://www.youtube.com/watch?v=epDEhAdpAGQ',
            ],
            [
                'judul' => 'Fogging vs Pencegahan Mandiri',
                'isi' => 'Perbandingan efektivitas antara fogging dan pencegahan mandiri.',
                'tautan' => 'https://www.youtube.com/watch?v=fUiyTA46Jbs',
            ],
        ];

        $articles = [
            [
                'judul' => 'Apa Itu DBD dan Bagaimana Penyebarannya?',
                'isi' => 'Penjelasan ilmiah mengenai DBD dan bagaimana virus menyebar.',
                'tautan' => 'https://www.alodokter.com/demam-berdarah',
            ],
            [
                'judul' => 'Tips Mencegah DBD Selama Musim Hujan',
                'isi' => 'Tips berguna untuk menghindari penyebaran DBD saat musim hujan.',
                'tautan' => 'https://hellosehat.com/infeksi/demam-berdarah/tips-cegah-dbd-musim-hujan/',
            ],
            [
                'judul' => 'Vaksin untuk DBD, Apakah Aman?',
                'isi' => 'Diskusi tentang penggunaan vaksin dengue dan efektivitasnya.',
                'tautan' => 'https://www.cnnindonesia.com/gaya-hidup/20230901161450-255-995763/vaksin-dbd-apa-efek-sampingnya-dan-siapa-saja-yang-boleh',
            ],
            [
                'judul' => 'Mengelola Sampah untuk Cegah DBD',
                'isi' => 'Mengelola lingkungan bersih sebagai salah satu bentuk pencegahan.',
                'tautan' => 'https://nasional.kompas.com/read/2021/11/08/14474511/kebersihan-lingkungan-jadi-kunci-cegah-dbd',
            ],
            [
                'judul' => 'Peran Masyarakat dalam Mencegah DBD',
                'isi' => 'Artikel ini membahas pentingnya partisipasi masyarakat dalam pencegahan DBD.',
                'tautan' => 'https://tirto.id/peran-aktif-masyarakat-dalam-cegah-dbd-fU8q',
            ],
        ];

        foreach ($videos as $video) {
            DB::table('edukasi')->insert([
                'judul' => $video['judul'],
                'isi' => $video['isi'],
                'tipe' => 'Video',
                'kategori_pengguna' => 'Warga',
                'tautan' => $video['tautan'],
                'dibuat_pada' => $now,
            ]);
        }

        foreach ($articles as $article) {
            DB::table('edukasi')->insert([
                'judul' => $article['judul'],
                'isi' => $article['isi'],
                'tipe' => 'Artikel',
                'kategori_pengguna' => 'Warga',
                'tautan' => $article['tautan'],
                'dibuat_pada' => $now,
            ]);
        }
    }
}
