<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaderSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar RT yang sudah tersedia. Sesuaikan jika rt_id berbeda.
        $dataKader = [
            [
                'nama_lengkap' => 'Siti Aminah',
                'telepon' => '081234567891',
                'password' => Hash::make('password123'),
                'rt_id' => 1,
            ],
        ];

        foreach ($dataKader as $kader) {
            DB::table('kader')->insert([
                'nama_lengkap' => $kader['nama_lengkap'],
                'telepon' => $kader['telepon'],
                'password' => $kader['password'],
                'rt_id' => $kader['rt_id'],
                'dibuat_pada' => now(),
                'updated_at' => null,
            ]);
        }
    }
}
