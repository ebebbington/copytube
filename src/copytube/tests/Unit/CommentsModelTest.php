<?php

namespace Tests\Unit;

use App\CommentsModel;
use App\TestModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use \Illuminate\Container\Container as Container;
use \Illuminate\Support\Facades\Facade as Facade;

class CommentsModelTest extends TestCase
{
    public function testFormattingDates ()
    {
        $CommentsModel = new CommentsModel;
        $comments = $CommentsModel->SelectQuery(['limit' => 10]);
        $formattedComments = $CommentsModel->formatDates($comments);
        foreach ($formattedComments as $formattedComment) {
            $this->assertEquals(1, preg_match("/^([0-3]\d{1})\/((0|1|2)\d{1})\/((19|20)\d{2})/", $formattedComment->date_posted));
        }
    }

    public function testConvertingSingleDate ()
    {
        $CommentsModel = new CommentsModel;
        $formattedDate = $CommentsModel->convertDate('2020-03-22');
        $this->assertEquals('22/03/2020', $formattedDate);
    }

    public function testGetAllByVideoTitleAndJoinProfilePictures ()
    {
        $CommentsModel = new CommentsModel;
        $comments = $CommentsModel->getAllByVideoTitleAndJoinProfilePicture('Something More');
        $this->assertEquals(false, empty($comments));
        foreach ($comments as $comment) {
            $this->assertEquals(true, property_exists($comment, 'profile_picture'));
        }
        $redisData = Cache::get('db:comments:videoTitle=Something+More');
        $this->assertEquals(true, isset($redisData) && !empty($redisData));
    }

    public function testCreatingComment ()
    {
        $CommentsModel = new CommentsModel;
        $data = [
            'comment' => 'Test',
            'author' => 'Test',
            'date_posted' => '2020-03-02',
            'video_posted_on' => 'Test',
            'user_id' => 1
        ];
        $comment = $CommentsModel->createComment($data);
        $this->assertEquals(true, isset($comment) && !empty($comment));
    }
}
