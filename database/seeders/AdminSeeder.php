<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu
        Admin::truncate();

        // Data admin utama
        Admin::create([
            'username' => 'superadmin',
            'password' => Hash::make('password123'), // Ganti dengan password yang kuat
            'nama_lengkap' => 'Super Administrator'
        ]);

        // Data admin tambahan (opsional)
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Ganti dengan password yang kuat
            'nama_lengkap' => 'Administrator Biasa'
        ]);

        // Jika ingin membuat banyak data dummy
        // Admin::factory(5)->create();
    }
}