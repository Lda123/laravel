<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForumPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Posting pertama oleh warga
        $post1 = DB::table('forum_post')->insertGetId([
            'warga_id' => 1, // ID warga pertama
            'kader_id' => null, // Postingan oleh warga, bukan kader
            'parent_id' => null, // Postingan utama
            'topik' => 'Keluhan tentang Fasilitas Kesehatan',
            'pesan' => 'Fasilitas kesehatan di daerah saya kurang memadai, apakah ada solusi untuk ini?',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Komentar pertama pada postingan pertama oleh kader
        DB::table('forum_post')->insert([
            'warga_id' => null,
            'kader_id' => 1, // ID kader pertama
            'parent_id' => $post1, // Merujuk ke postingan pertama
            'topik' => null,
            'pesan' => 'Terima kasih atas masukan Anda. Kami akan memperhatikan masalah ini dan mencari solusi terbaik.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Posting kedua oleh warga
        $post2 = DB::table('forum_post')->insertGetId([
            'warga_id' => 2, // ID warga kedua
            'kader_id' => null, // Postingan oleh warga, bukan kader
            'parent_id' => null, // Postingan utama
            'topik' => 'Penyuluhan tentang Pencegahan Demam Berdarah',
            'pesan' => 'Apakah ada penyuluhan terbaru tentang pencegahan demam berdarah di wilayah saya?',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Komentar kedua pada postingan kedua oleh kader
        DB::table('forum_post')->insert([
            'warga_id' => null,
            'kader_id' => 2, // ID kader kedua
            'parent_id' => $post2, // Merujuk ke postingan kedua
            'topik' => null,
            'pesan' => 'Kami akan mengadakan penyuluhan pada minggu depan. Silakan hadir!',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Posting ketiga oleh kader
        $post3 = DB::table('forum_post')->insertGetId([
            'warga_id' => null,
            'kader_id' => 3, // ID kader ketiga
            'parent_id' => null, // Postingan utama
            'topik' => 'Laporan Kegiatan Sosialisasi',
            'pesan' => 'Kami baru saja menyelesaikan kegiatan sosialisasi di RT 1 dan RT 2.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Komentar ketiga pada postingan ketiga oleh warga
        DB::table('forum_post')->insert([
            'warga_id' => 3, // ID warga ketiga
            'kader_id' => null, // Postingan oleh warga
            'parent_id' => $post3, // Merujuk ke postingan ketiga
            'topik' => null,
            'pesan' => 'Terima kasih atas kegiatan sosialisasinya, sangat bermanfaat.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Posting keempat oleh warga
        $post4 = DB::table('forum_post')->insertGetId([
            'warga_id' => 3, // ID warga ketiga
            'kader_id' => null, // Postingan oleh warga
            'parent_id' => null, // Postingan utama
            'topik' => 'Sosialisasi Pembentukan Posko Kesehatan',
            'pesan' => 'Kami perlu mendiskusikan pembentukan posko kesehatan di lingkungan kami.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Komentar keempat pada postingan keempat oleh kader
        DB::table('forum_post')->insert([
            'warga_id' => null,
            'kader_id' => 1, // ID kader pertama
            'parent_id' => $post4, // Merujuk ke postingan keempat
            'topik' => null,
            'pesan' => 'Kader akan siap membantu dalam pembentukan posko kesehatan.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Posting kelima oleh warga
        $post5 = DB::table('forum_post')->insertGetId([
            'warga_id' => 2, // ID warga kedua
            'kader_id' => null, // Postingan oleh warga
            'parent_id' => null, // Postingan utama
            'topik' => 'Masalah Pembuangan Sampah',
            'pesan' => 'Banyak sampah yang menumpuk di sekitar lingkungan saya. Apa yang bisa dilakukan?',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);

        // Komentar kelima pada postingan kelima oleh kader
        DB::table('forum_post')->insert([
            'warga_id' => null,
            'kader_id' => 2, // ID kader kedua
            'parent_id' => $post5, // Merujuk ke postingan kelima
            'topik' => null,
            'pesan' => 'Kami akan segera menanggapi masalah ini dan melakukan pembersihan.',
            'gambar' => null,
            'dibuat_pada' => now(),
        ]);
    }
}
