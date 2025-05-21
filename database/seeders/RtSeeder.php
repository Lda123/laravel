<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rt;

class RtSeeder extends Seeder
{
    public function run(): void
    {
        Rt::create([
            'rw_id' => 1,
            'kelurahan_id' => 1,
            'nomor_rt' => '01',
            'koordinat_lat' => -7.24140000,
            'koordinat_lng' => 112.70190000,
        ]);
    }
}
