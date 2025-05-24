<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            'Asemrowo', 'Benowo', 'Bubutan', 'Bulak', 'Dukuh Pakis',
            'Gayungan', 'Genteng', 'Gubeng', 'Gunung Anyar', 'Jambangan',
            'Karang Pilang', 'Kenjeran', 'Krembangan', 'Lakarsantri', 'Mulyorejo',
            'Pabean Cantikan', 'Pakal', 'Rungkut', 'Sambikerep', 'Sawahan',
            'Semampir', 'Simokerto', 'Sukolilo', 'Sukomanunggal', 'Tambaksari'
        ];

        foreach ($kecamatans as $nama) {
            Kecamatan::create(['nama_kecamatan' => $nama]);
        }
    }
}
