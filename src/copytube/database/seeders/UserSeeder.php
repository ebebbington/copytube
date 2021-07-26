<?php

namespace Database\Seeders;

use Seeder;
use DB;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "id" => 21,
            "username" => "Edward Home",
            "email_address" => "EdwardSBebbington@hotmail.com",
            "password" => Hash::make("Welcome1"),
            "logged_in" => 1,
            "login_attempts" => 3,
            "profile_picture" => "img/lava_sample.jpg",
        ]);
        DB::table("users")->insert([
            "id" => 23,
            "username" => "test",
            "email_address" => "test@hotmail.com",
            "password" => Hash::make("Welcome1"),
            "logged_in" => 1,
            "login_attempts" => 3,
            "profile_picture" => "img/something_more.jpg",
        ]);
    }
}
