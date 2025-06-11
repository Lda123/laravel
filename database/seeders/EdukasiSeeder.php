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

        $videosWarga = [
            [
                'judul' => 'Pencegahan DBD di Lingkungan Rumah',
                'isi' => 'Video ini membahas cara sederhana mencegah DBD dari rumah sendiri.',
                'tautan' => 'https://youtu.be/VwM6ruhcb78?si=sU7KWUfcSOBFCwj5',
                'thumbnail_url' => 'https://i.ytimg.com/vi/VwM6ruhcb78/maxresdefault.jpg',
                'durasi' => '05:30',
                'kategori' => 'Pencegahan',
            ],
            [
                'judul' => 'Kenali Gejala DBD Sejak Dini',
                'isi' => 'Pelajari gejala umum DBD agar dapat ditangani lebih cepat.',
                'tautan' => 'https://youtu.be/ph_n7YiKURc?si=naLFOHXwh4FiKdcp',
                'thumbnail_url' => 'https://i.ytimg.com/vi/ph_n7YiKURc/maxresdefault.jpg',
                'durasi' => '05:15',
                'kategori' => 'Gejala',
            ],
            [
                'judul' => 'Cara Kerja Nyamuk Aedes Aegypti',
                'isi' => 'Penjelasan mengenai siklus hidup dan bahaya nyamuk penyebar DBD.',
                'tautan' => 'https://youtu.be/Ai9VZRIUN94?si=a8LSfVWqoZg1VZtt',
                'thumbnail_url' => 'https://i.ytimg.com/vi/Ai9VZRIUN94/maxresdefault.jpg',
                'durasi' => '04:45',
                'kategori' => 'Penyebab',
            ],
            [
                'judul' => 'Langkah 3M Plus Cegah DBD',
                'isi' => 'Penjelasan metode 3M Plus dalam memberantas DBD.',
                'tautan' => 'https://youtu.be/JLz7Al42sZc?si=aZDlAqT5YExeT_KN',
                'thumbnail_url' => 'https://i.ytimg.com/vi/JLz7Al42sZc/maxresdefault.jpg',
                'durasi' => '01:20',
                'kategori' => 'Pencegahan',
            ],
        ];
        $articlesWarga = [
            [
                'judul' => 'Apa Itu DBD dan Bagaimana Penyebarannya?',
                'isi' => 'Penjelasan ilmiah mengenai DBD dan bagaimana virus menyebar.',
                'tautan' => 'https://www.alodokter.com/demam-berdarah',
                'thumbnail_url' => 'https://cdn.alodokter.com/media/article/20170807111203/shutterstock-408470024.jpg',
                'kategori' => 'Pengetahuan Dasar',
            ],
            [
                'judul' => 'Tips Mencegah DBD Selama Musim Hujan',
                'isi' => 'Tips berguna untuk menghindari penyebaran DBD saat musim hujan.',
                'tautan' => 'https://hellosehat.com/infeksi/demam-berdarah/tips-cegah-dbd-musim-hujan/',
                'thumbnail_url' => 'https://cdn.hellosehat.com/wp-content/uploads/2020/10/cegah-dbd.jpg',
                'kategori' => 'Pencegahan',
            ],
            [
                'judul' => 'Vaksin untuk DBD, Apakah Aman?',
                'isi' => 'Diskusi tentang penggunaan vaksin dengue dan efektivitasnya.',
                'tautan' => 'https://www.cnnindonesia.com/gaya-hidup/20230901161450-255-995763/vaksin-dbd-apa-efek-sampingnya-dan-siapa-saja-yang-boleh',
                'thumbnail_url' => 'https://akcdn.detik.net.id/community/media/visual/2023/07/06/vaksin-dbd_169.jpeg',
                'kategori' => 'Pengobatan',
            ],
            [
                'judul' => 'Mengelola Sampah untuk Cegah DBD',
                'isi' => 'Mengelola lingkungan bersih sebagai salah satu bentuk pencegahan.',
                'tautan' => 'https://nasional.kompas.com/read/2021/11/08/14474511/kebersihan-lingkungan-jadi-kunci-cegah-dbd',
                'thumbnail_url' => 'https://asset.kompas.com/crops/IsOYbK1xL3a-u6lfZUt9-dhCnMc=/0x0:800x533/750x500/data/photo/2021/11/08/6188e81cebd7b.jpg',
                'kategori' => 'Lingkungan',
            ],
            [
                'judul' => 'Peran Masyarakat dalam Mencegah DBD',
                'isi' => 'Artikel ini membahas pentingnya partisipasi masyarakat dalam pencegahan DBD.',
                'tautan' => 'https://tirto.id/peran-aktif-masyarakat-dalam-cegah-dbd-fU8q',
                'thumbnail_url' => 'https://mmc.tirto.id/image/otf/500x0/2017/12/15/demam-berdarah-istock-ratio.jpg',
                'kategori' => 'Sosial',
            ],
        ];
        
        $videosKader = [
            [
                'judul' => 'Pelatihan Kader DBD Tingkat Dasar',
                'isi' => 'Materi pelatihan dasar untuk kader dalam penanganan DBD.',
                'tautan' => 'https://www.youtube.com/watch?v=h3yYmgit4CM',
                'thumbnail_url' => 'https://img.youtube.com/vi/h3yYmgit4CM/maxresdefault.jpg',
                'durasi' => '12:30',
                'kategori' => 'Pelatihan',
            ],
            [
                'judul' => 'Teknik PSN (Pemberantasan Sarang Nyamuk)',
                'isi' => 'Panduan lengkap melaksanakan PSN untuk kader DBD.',
                'tautan' => 'https://youtu.be/fIyoay9j-W8?si=jB2ec1jP8CaFiLk1',
                'thumbnail_url' => 'https://img.youtube.com/vi/fIyoay9j-W8/maxresdefault.jpg',
                'durasi' => '15:45',
                'kategori' => 'Teknis',
            ],
            [
                'judul' => 'Pendataan dan Pelaporan Kasus DBD',
                'isi' => 'Cara melakukan pendataan dan pelaporan kasus DBD yang efektif.',
                'tautan' => 'https://youtu.be/GRbSJ4dGunc?si=F74pjujKzbirlVd5',
                'thumbnail_url' => 'https://img.youtube.com/vi/GRbSJ4dGunc/maxresdefault.jpg',
                'durasi' => '09:20',
                'kategori' => 'Administrasi',
            ],
        ];

        // Insert data untuk warga
        foreach ($videosWarga as $video) {
            DB::table('edukasi')->insert([
                'judul' => $video['judul'],
                'isi' => $video['isi'],
                'tipe' => 'Video',
                'kategori_pengguna' => 'Warga',
                'tautan' => $video['tautan'],
                'thumbnail_url' => $video['thumbnail_url'],
                'durasi' => $video['durasi'],
                'kategori' => $video['kategori'],
                'views' => rand(100, 1000),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($articlesWarga as $article) {
            DB::table('edukasi')->insert([
                'judul' => $article['judul'],
                'isi' => $article['isi'],
                'tipe' => 'Artikel',
                'kategori_pengguna' => 'Warga',
                'tautan' => $article['tautan'],
                'thumbnail_url' => $article['thumbnail_url'],
                'kategori' => $article['kategori'],
                'views' => rand(50, 500),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Insert data untuk kader
        foreach ($videosKader as $video) {
            DB::table('edukasi')->insert([
                'judul' => $video['judul'],
                'isi' => $video['isi'],
                'tipe' => 'Video',
                'kategori_pengguna' => 'Kader',
                'tautan' => $video['tautan'],
                'thumbnail_url' => $video['thumbnail_url'],
                'durasi' => $video['durasi'],
                'kategori' => $video['kategori'],
                'views' => rand(30, 300),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}