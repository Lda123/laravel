<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Rw;

class RwSeeder extends Seeder {
    public function run(): void {
        Rw::create(['kelurahan_id' => 1, 'nomor_rw' => '01']);
    }
}
