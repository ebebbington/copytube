<?php

namespace Database\Seeders;

use Seeder;
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
        var_dump(CommentSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(VideoSeeder::class);
    }
}
