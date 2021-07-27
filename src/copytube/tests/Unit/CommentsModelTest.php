<?php

namespace Tests\Unit;

use App\CommentsModel;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsModelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    // public function testFormattingDates()
    // {
    //     $CommentsModel = new CommentsModel();
    //     var_dump($CommentsModel->SelectQuery(['limit', 2]));
    //     $comments = $CommentsModel->SelectQuery(["limit" => 10]);
    //     $formattedComments = $CommentsModel->formatDates($comments);
    //     foreach ($formattedComments as $formattedComment) {
    //         $this->assertEquals(
    //             1,
    //             preg_match(
    //                 "/^([0-3]\d{1})\/((0|1|2)\d{1})\/((19|20)\d{2})/",
    //                 $formattedComment->date_posted
    //             )
    //         );
    //     }
    // }

    // public function testConvertingSingleDate()
    // {
    //     $CommentsModel = new CommentsModel();
    //     $formattedDate = $CommentsModel->convertDate("2020-03-22");
    //     $this->assertEquals("22/03/2020", $formattedDate);
    // }

    public function testGetAllByVideoIdJoinUserProfilePic()
    {
        $CommentsModel = new CommentsModel();
        $comments = $CommentsModel->getAllByVideoIdJoinUserProfilePic(1);
        $this->assertEquals(false, empty($comments));
        foreach ($comments as $comment) {
            $this->assertEquals(
                true,
                property_exists($comment, "profile_picture")
            );
        }

        $Cache = new Cache();
        $redisData = $Cache::get("db:comments:videoId=1");
        $this->assertEquals(true, isset($redisData) && !empty($redisData));

        // And when no comments are found
        $comments = $CommentsModel->getAllByVideoIdJoinUserProfilePic(999);
        $this->assertTrue($comments === []);
    }

    public function testCreatingComment()
    {
        $CommentsModel = new CommentsModel();
        $data = [
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-03-02",
            "video_id" => 3,
            "user_id" => 21,
        ];
        $comment = $CommentsModel->createComment($data);
        $CommentsModel->DeleteQuery(["comment" => "Test"]);
        $this->assertEquals(true, isset($comment) && !empty($comment));
    }
}
