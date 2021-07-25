<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('videos')->insert([
            'title' => 'Something More',
            'description' => 'Watch this inspirational video as we look at all of the beautiful things inside this world',
            'src' => 'videos/something_more.mp4',
            'poster' => 'img/something_more.jpg'
        ]);
        DB::table('videos')->insert([
            'title' => 'Lava Sample',
            'description' => 'Watch this lava flow through the earth, burning and sizzling as it progresses',
            'src' => 'videos/lava_sample.mp4',
            'poster' => 'img/lava_sample.jpg'
        ]);
        DB::table('videos')->insert([
            'title' => 'An Iceland Venture',
            'description' => 'Iceland, beautiful and static, watch as we venture through this glorious place',
            'src' => 'videos/an_iceland_venture.mp4',
            'poster' => 'img/an_iceland_venture.jpg'
        ]);
    }
}
