<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warga')->insert([
            [
                'nik' => '1234567890123456',
                'nama_lengkap' => 'Lisa Dwi',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Perempuan',
                'alamat_lengkap' => 'Jl. Contoh No.1',
                'rt_id' => 1,
                'telepon' => '08123456789',
                'password' => Hash::make('password123'),
                'foto_ktp' => 'ktp1.png',
                'foto_diri_ktp' => 'diri1.png',
                'profile_pict' => null,
            ],
            [
                'nik' => '2345678901234567',
                'nama_lengkap' => 'Budi Santoso',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-02-15',
                'jenis_kelamin' => 'Laki-laki',
                'alamat_lengkap' => 'Jl. Merdeka No.3',
                'rt_id' => 1,
                'telepon' => '08123456780',
                'password' => Hash::make('password456'),
                'foto_ktp' => 'ktp2.png',
                'foto_diri_ktp' => 'diri2.png',
                'profile_pict' => null,
            ],
            [
                'nik' => '3456789012345678',
                'nama_lengkap' => 'Siti Aminah',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-08-20',
                'jenis_kelamin' => 'Perempuan',
                'alamat_lengkap' => 'Jl. Raya No.4',
                'rt_id' => 1,
                'telepon' => '08123456781',
                'password' => Hash::make('password789'),
                'foto_ktp' => 'ktp3.png',
                'foto_diri_ktp' => 'diri3.png',
                'profile_pict' => null,
            ],
        ]);
    }
}
