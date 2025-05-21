<?php
namespace Database\Seeders;
// database/seeders/WargaSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Warga;
use Illuminate\Support\Facades\Hash;

class WargaSeeder extends Seeder {
    public function run(): void {
        Warga::create([
            'nik' => '1234567890123456',
            'nama_lengkap' => 'Lisa Dwi',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'Perempuan',
            'alamat_lengkap' => 'Jl. Contoh No.1',
            'rt_id' => 1,
            'telepon' => '08123456789',
            'password' => Hash::make('password123'),
            'foto_ktp' => 'ktp.png',
            'foto_diri_ktp' => 'diri.png',
            'profile_pict' => null, // default null
        ]);
        
    }
}
