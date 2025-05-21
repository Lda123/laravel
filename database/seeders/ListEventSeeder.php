<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'nama_event' => 'Penyuluhan DBD',
                'tanggal' => Carbon::today()->addDays(2),
                'lokasi' => 'Balai RW 01',
                'waktu' => '09:00 - 11:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Fogging Massal',
                'tanggal' => Carbon::today()->addDays(5),
                'lokasi' => 'Wilayah RT 03',
                'waktu' => '07:00 - 10:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Pelatihan Juru Pemantau Jentik',
                'tanggal' => Carbon::today()->addWeek(),
                'lokasi' => 'Balai RW 05',
                'waktu' => '13:00 - 16:00',
                'biaya' => 'Rp10.000',
                'kategori_pengguna' => 'kader',
            ],
            [
                'nama_event' => 'Lomba Bersih Lingkungan',
                'tanggal' => Carbon::today()->addDays(10),
                'lokasi' => 'Lingkungan RW 07',
                'waktu' => '08:00 - 12:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Workshop Daur Ulang',
                'tanggal' => Carbon::today()->addDays(3),
                'lokasi' => 'Balai Warga RW 02',
                'waktu' => '14:00 - 17:00',
                'biaya' => 'Rp5.000',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Senam Sehat Bersama',
                'tanggal' => Carbon::today()->addDays(1),
                'lokasi' => 'Lapangan RW 04',
                'waktu' => '06:30 - 08:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Sosialisasi Gizi Anak',
                'tanggal' => Carbon::today()->addDays(7),
                'lokasi' => 'Puskesmas Setempat',
                'waktu' => '10:00 - 12:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Pemeriksaan Kesehatan Gratis',
                'tanggal' => Carbon::today()->addDays(6),
                'lokasi' => 'Balai RW 06',
                'waktu' => '09:00 - 13:00',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
            [
                'nama_event' => 'Pelatihan Kader DBD',
                'tanggal' => Carbon::today()->addDays(4),
                'lokasi' => 'Gedung Serbaguna',
                'waktu' => '15:00 - 17:00',
                'biaya' => 'Rp15.000',
                'kategori_pengguna' => 'kader',
            ],
            [
                'nama_event' => 'Edukasi Hidup Bersih dan Sehat',
                'tanggal' => Carbon::today()->addDays(8),
                'lokasi' => 'Balai RW 08',
                'waktu' => '10:00 - 11:30',
                'biaya' => 'Gratis',
                'kategori_pengguna' => 'warga',
            ],
        ];

        foreach ($events as $event) {
            DB::table('list_event')->insert(array_merge($event, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
