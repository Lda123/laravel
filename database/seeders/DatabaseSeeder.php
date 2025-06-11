<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;                                                                                                                                                                                                                                                                                                                      
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(KecamatanSeeder::class);
        $this->call(KelurahanSeeders::class);
        $this->call(RwSeeder::class);
        $this->call(RtSeeder::class);
        $this->call(WargaSeeder::class);
        $this->call(KaderSeeder::class);
        $this->call(TrackingHarianSeeder::class);
        $this ->call(ListEventSeeder::class);
        $this->call(EdukasiSeeder::class);
        $this->call(ForumPostSeeder::class);
        $this->call(BukuPanduanSeeder::class);

    }
}                                                                                              