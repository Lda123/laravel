<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Kelurahan;

class KelurahanSeeders extends Seeder {
    public function run(): void {
        Kelurahan::create(['kecamatan_id' => 1, 'nama_kelurahan' => 'Asemrowo']);
    }
}
