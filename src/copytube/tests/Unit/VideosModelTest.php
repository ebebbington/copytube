<?php

namespace Tests\Unit;

use App\VideosModel;
use Tests\TestCase;

class VideosModelTest extends TestCase
{
    public function testGetVideoByTitleMethod ()
    {
        $VideosModel = new VideosModel;
        $video = $VideosModel->getVideoByTitle('Something More');
        $this->assertEquals(true, isset($video) && !empty($video));
        $video = $VideosModel->getVideoByTitle('I dont exist');
        $this->assertEquals(true, $video === false);
    }

    public function testGetRabbitHoleVideosMethod ()
    {
        $VideosModel = new VideosModel;
        $videos = $VideosModel->getRabbitHoleVideos('Lava Sample');
        $this->assertEquals(true, isset($videos) && !empty($videos) && sizeof($videos) === 2);
    }
}
