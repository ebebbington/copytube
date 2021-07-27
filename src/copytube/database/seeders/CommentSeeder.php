<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("comments")->insert([
            "comment" =>
                "Super long comment to test how a comment would display when its content is so large that it might end up not overflowing correctly for example it might just keep displaying on the right and move out of view which we do not want do we",
            "author" => "Edward Home",
            "date_posted" => "2019-03-08",
            "video_id" => 1,
            "user_id" => 21,
        ]);
        DB::table("comments")->insert([
            "comment" => "test comment lava sample",
            "author" => "Edward Home",
            "date_posted" => "2019-03-08",
            "video_id" => 2,
            "user_id" => 21,
        ]);
        DB::table("comments")->insert([
            "comment" => "test comment iceland",
            "author" => "Edward Home",
            "date_posted" => "2019-03-08",
            "video_id" => 3,
            "user_id" => 21,
        ]);
    }
}
