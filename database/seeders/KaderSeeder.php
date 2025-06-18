<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kader')->insert([
            [
                'nama_lengkap' => 'Rina Sari',
                'telepon' => '08123456790',
                'password' => Hash::make('passwordKader123'),
                'profil_pict' => null, // Add this line
                'rt_id' => 1,
                'dibuat_pada' => now(),
            ],
            [
                'nama_lengkap' => 'Joko Prabowo',
                'telepon' => '08123456791',
                'password' => Hash::make('passwordKader456'),
                'profil_pict' => null, // Add this line
                'rt_id' => 2,
                'dibuat_pada' => now(),
            ],
            [
                'nama_lengkap' => 'Ayu Lestari',
                'telepon' => '08123456792',
                'password' => Hash::make('passwordKader789'),
                'profil_pict' => null, // Add this line
                'rt_id' => 3,
                'dibuat_pada' => now(),
            ],
        ]);
    }
}