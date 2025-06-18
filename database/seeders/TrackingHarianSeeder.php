<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Warga;
use Carbon\Carbon;

class TrackingHarianSeeder extends Seeder
{
    public function run(): void
    {
        $wargaList = DB::table('warga')->get();
        $today = Carbon::today();
        $startDate = $today->copy()->subMonths(2); // 2 bulan terakhir

        foreach ($wargaList as $warga) {
            $date = $startDate->copy();

            // Loop setiap 7 hari (1x seminggu)
            while ($date <= $today) {
                DB::table('tracking_harian')->insert([
                    'warga_id' => $warga->id,
                    'warga_nik' => $warga->nik,
                    'nama_warga' => $warga->nama_lengkap,
                    'kader_id' => null, // atau bisa kamu set sesuai RT-nya
                    'tanggal' => $date->format('Y-m-d'),
                    'kategori_masalah' => rand(1, 10) > 3 ? 'Aman' : 'Tidak Aman', // ±70% Aman
                    'deskripsi' => null,
                    'bukti_foto' => null,
                    'status' => 'Selesai',
                    'dibuat_pada' => $date->copy()->setTime(rand(7, 17), rand(0, 59)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $date->addDays(7); // hanya isi setiap seminggu
            }
        }
    }
}
