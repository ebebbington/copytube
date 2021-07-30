<?php

namespace Database\Seeders;

use App\Comment;
use App\User;
use App\Video;
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
        $user = User::first();
        $videos = Video::all();
        $date = "2019-03-08";
        DB::table("comments")->insert([
            "comment" =>
                "Super long comment to test how a comment would display when its content is so large that it might end up not overflowing correctly for example it might just keep displaying on the right and move out of view which we do not want do we" .
                $videos[0]["title"],
            "author" => $user["username"],
            "date_posted" => $date,
            "video_id" => $videos[0]["id"],
            "user_id" => $user["id"],
        ]);
        DB::table("comments")->insert([
            "comment" => "test comment " . $videos[1]["title"],
            "author" => $user["username"],
            "date_posted" => $date,
            "video_id" => $videos[1]["id"],
            "user_id" => $user["id"],
        ]);
        DB::table("comments")->insert([
            "comment" => "test comment " . $videos[2]["title"],
            "author" => "Edward Home",
            "date_posted" => $date,
            "video_id" => $videos[2]["id"],
            "user_id" => $user["id"],
        ]);
    }
}
