<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelurahan;

class KelurahanSeeders extends Seeder
{
    public function run(): void
    {
        $kelurahans = [
            [1, 'Asemrowo'],
            [1, 'Genting Kalianak'],
            [1, 'Tambak Sarioso'],
            [2, 'Kandangan'],
            [2, 'Romokalisari'],
            [2, 'Sememi'],
            [2, 'Tambak Oso Wilangun'],
            [3, 'Alun-alun Contong'],
            [3, 'Bubutan'],
            [3, 'Gundih'],
            [3, 'Jepara'],
            [3, 'Tembok Dukuh'],
            [4, 'Bulak'],
            [4, 'Kedung Cowek'],
            [4, 'Kenjeran'],
            [4, 'Sukolilo Baru'],
            [5, 'Dukuh Kupang'],
            [5, 'Dukuh Pakis'],
            [5, 'Gunung Sari'],
            [5, 'Pradah Kalikendal'],
            [6, 'Dukuh Menanggal'],
            [6, 'Gayungan'],
            [7, 'Embong Kaliasin'],
            [7, 'Genteng'],
            [7, 'Kapasari']
        ];

        foreach ($kelurahans as [$kecamatan_id, $nama_kelurahan]) {
            Kelurahan::create([
                'kecamatan_id' => $kecamatan_id,
                'nama_kelurahan' => $nama_kelurahan
            ]);
        }
    }
}
