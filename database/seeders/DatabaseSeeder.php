<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Seeds\IndonesiaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (class_exists(IndonesiaSeeder::class) && Province::count() === 0) {
            $this->call(IndonesiaSeeder::class);
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
