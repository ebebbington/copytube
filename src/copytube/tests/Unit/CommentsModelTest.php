<?php

namespace Tests\Unit;

use App\CommentsModel;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CommentsModelTest extends TestCase
{
    public function testFormattingDates()
    {
        $CommentsModel = new CommentsModel();
        $comments = $CommentsModel->SelectQuery(["limit" => 10]);
        $formattedComments = $CommentsModel->formatDates($comments);
        foreach ($formattedComments as $formattedComment) {
            $this->assertEquals(
                1,
                preg_match(
                    "/^([0-3]\d{1})\/((0|1|2)\d{1})\/((19|20)\d{2})/",
                    $formattedComment->date_posted
                )
            );
        }
    }

    public function testConvertingSingleDate()
    {
        $CommentsModel = new CommentsModel();
        $formattedDate = $CommentsModel->convertDate("2020-03-22");
        $this->assertEquals("22/03/2020", $formattedDate);
    }

    public function testGetAllByVideoTitleAndJoinProfilePictures()
    {
        $CommentsModel = new CommentsModel();
        $comments = $CommentsModel->getAllByVideoTitleAndJoinProfilePicture(
            "Something More"
        );
        $this->assertEquals(false, empty($comments));
        foreach ($comments as $comment) {
            $this->assertEquals(
                true,
                property_exists($comment, "profile_picture")
            );
        }

        $Cache = new Cache();
        $redisData = $Cache::get("db:comments:videoTitle=Something+More");
        $this->assertEquals(true, isset($redisData) && !empty($redisData));

        // And when no comments are found
        $comments = $CommentsModel->getAllByVideoTitleAndJoinProfilePicture(
            "I dont exist"
        );
        $this->assertTrue($comments === []);
    }

    public function testCreatingComment()
    {
        $CommentsModel = new CommentsModel();
        $data = [
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-03-02",
            "video_posted_on" => "Test",
            "user_id" => 1,
        ];
        $comment = $CommentsModel->createComment($data);
        $this->assertEquals(true, isset($comment) && !empty($comment));
    }
}
