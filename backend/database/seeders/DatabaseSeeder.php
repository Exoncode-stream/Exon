<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Reproduces the initial data from the legacy init_db.php script.
     */
    public function run(): void
    {
        // --- Admin User ---
        DB::table('users')->insertOrIgnore([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- Links ---
        DB::table('links')->insertOrIgnore([
            ['name' => 'YouTube', 'url' => 'https://www.youtube.com/@exon9858'],
            ['name' => 'GitHub', 'url' => 'https://github.com/Exoncode-stream/'],
            ['name' => 'Discord', 'url' => 'guiireg'],
        ]);

        // --- Videos ---
        DB::table('videos')->insertOrIgnore([
            [
                'title' => 'Creating Learn Code website with Next.js',
                'youtube_id' => 'https://www.youtube.com/watch?v=ILW91gXl30Y',
                'category' => 'Web development',
            ],
        ]);

        // --- Articles ---
        DB::table('articles')->insertOrIgnore([
            [
                'title' => 'Introduction to Next.js',
                'content' => 'Next.js is a React framework that gives you building blocks to create web applications...',
            ],
            [
                'title' => 'Understanding Docker',
                'content' => 'Docker is a set of platform as a service products that use OS-level virtualization to deliver software in packages called containers...',
            ],
        ]);
    }
}
