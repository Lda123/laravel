<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrackingHarian;
use Illuminate\Support\Facades\DB;

class TrackingHarianSeeder extends Seeder
{
    public function run(): void
    {
        $wargaId = DB::table('warga')->inRandomOrder()->value('id');
        $kaderId = DB::table('kader')->inRandomOrder()->value('id');

        TrackingHarian::create([
            'warga_id' => $wargaId,
            'status_kesehatan' => 'Sehat',
            'status_lingkungan' => 'Bersih',
            'kategori_masalah' => 'Belum Dicek',
            'deskripsi' => 'Lingkungan tampak bersih, tidak ada genangan air.',
            'bukti_foto' => 'foto_bukti.jpg',
            'tanggal_pantau' => now()->toDateString(),
            'kader_id' => $kaderId,
            'dibuat_pada' => now(),
        ]);
    }
}
