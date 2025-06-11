<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrackingHarian;
use Carbon\Carbon;

class TrackingHarianSeeder extends Seeder
{
    public function run(): void
    {
        $kaderId = 1;
        $wargaId = 2;
        $wargaNik = '1234567890123456';
        $namaWarga = 'Budi Santoso';

        $tanggal = Carbon::create(2025, 2, 1)->startOfWeek(); // Mulai dari minggu pertama Februari 2025
        $endTanggal = Carbon::create(2025, 3, 31); // Akhir Maret 2025

        while ($tanggal->lte($endTanggal)) {
            TrackingHarian::create([
                'warga_id' => $wargaId,
                'warga_nik' => $wargaNik,
                'nama_warga' => $namaWarga,
                'kader_id' => $kaderId,
                'tanggal' => $tanggal->toDateString(),
                'kategori_masalah' => collect(['Aman', 'Tidak Aman', 'Belum Dicek'])->random(),
                'deskripsi' => 'Kondisi lingkungan dan kesehatan warga diperiksa.',
                'bukti_foto' => null,
                'status' => 'Selesai',
                'dibuat_pada' => now(),
            ]);

            // Tambah 7 hari untuk minggu berikutnya
            $tanggal->addWeek();
        }
    }
}
