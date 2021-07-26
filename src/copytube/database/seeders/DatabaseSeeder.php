<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\VideoSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(VideoSeeder::class);
        $this->call(CommentSeeder::class);
    }
}
