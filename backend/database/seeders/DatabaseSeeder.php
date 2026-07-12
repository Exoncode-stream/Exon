<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial application data.
     * Includes default administrator account, social links, sample videos,
     * and introductory articles.
     *
     * @return void
     */
    public function run(): void
    {
        // Create default administrator account
        DB::table('users')->insertOrIgnore([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Initialize social media and contact links
        DB::table('links')->insertOrIgnore([
            ['name' => 'YouTube', 'url' => 'https://www.youtube.com/@exon9858'],
            ['name' => 'GitHub', 'url' => 'https://github.com/Exoncode-stream/'],
            ['name' => 'Discord', 'url' => 'guiireg'],
        ]);

        // Seed introductory sample videos
        DB::table('videos')->insertOrIgnore([
            [
                'title' => 'Creating Learn Code website with Next.js',
                'youtube_id' => 'https://www.youtube.com/watch?v=ILW91gXl30Y',
                'category' => 'Web development',
            ],
        ]);

        // Populate initial platform articles
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
