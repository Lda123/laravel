<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rw;

class RwSeeder extends Seeder
{
    public function run(): void
    {
        $rws = [
            [1, '01'], [1, '02'], [1, '03'],
            [2, '01'], [2, '02'], [2, '03'],
            [3, '01'], [3, '02'], [3, '03'],
            [4, '01'], [4, '02'], [4, '03'],
            [5, '01'], [5, '02'], [5, '03'],
            [6, '01'], [6, '02'], [6, '03'],
            [7, '01'], [7, '02'], [7, '03'],
            [8, '01'], [8, '02'], [8, '03'],
            [9, '01'],
        ];

        foreach ($rws as [$kelurahan_id, $nomor_rw]) {
            Rw::create([
                'kelurahan_id' => $kelurahan_id,
                'nomor_rw' => $nomor_rw,
            ]);
        }
    }
}
