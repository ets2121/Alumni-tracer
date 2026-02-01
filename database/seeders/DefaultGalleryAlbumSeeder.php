<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GalleryAlbum;

class DefaultGalleryAlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GalleryAlbum::firstOrCreate(
            ['is_default' => true],
            [
                'name' => 'News & Events Uploads',
                'description' => 'System folder for News and Event featured images. Do not delete.',
                'category' => 'General',
                'is_default' => true
            ]
        );

        $this->command->info('Default "News & Events Uploads" album created successfully.');
    }
}
