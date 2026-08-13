<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Holiday & Travel',
                'slug'          => 'vacation-travel',
                'icon'          => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Family',
                'slug'          => 'family-gathering',
                'icon'          => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Marriage & Prewedding',
                'slug'          => 'wedding-prewedding',
                'icon'          => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Birthday',
                'slug'          => 'birthday-celebration',
                'icon'          => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Graduation & Wisuda',
                'slug'          => 'graduation-wisuda',
                'icon'          => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&q=80',
            ],
            [
                'nama_kategori' => 'Baby & Maternity',
                'slug'          => 'maternity-newborn',
                'icon'          => 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Event & Music Concert',
                'slug'          => 'event-concert',
                'icon'          => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&q=80',
            ],
            [
                'nama_kategori' => 'Studio & Personal Portrait',
                'slug'          => 'studio-portrait',
                'icon'          => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&q=80',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                [
                    'nama_kategori' => $cat['nama_kategori'],
                    'icon'          => $cat['icon'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }
    }
}
