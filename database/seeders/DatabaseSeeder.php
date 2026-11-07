<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (class_exists(ProvincesSeeder::class) && Province::count() === 0) {
            $this->call(ProvincesSeeder::class);
        }

        if (class_exists(CitiesSeeder::class) && City::count() === 0) {
            $this->call(CitiesSeeder::class);
        }

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PhotographerSeeder::class,
            BookingContractSeeder::class,
            PhotoContestSeeder::class,
            ContestSubmissionSeeder::class,
            ElearningCurriculumSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
