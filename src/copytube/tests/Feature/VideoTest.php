<?php

namespace Tests\Feature;

use App\Comment;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    private string $something_more_title = "Something More";

    private string $lava_sample_title = "Lava Sample";

    protected $seed = true;

    private int $lava_sample_id = 2;

    public function testPostCommentWithoutAuth()
    {
        // Run request without auth
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->post(
            TestUtilities::$video_comment_path,
            ["comment" => "test"],
            $headers
        );
        $res->assertStatus(302);
        $res->assertRedirect(TestUtilities::$login_path);
    }

    public function testDeleteCommentWithoutAuth()
    {
        $res = $this->delete("/video/comment?id=12345");
        $res->assertStatus(302);
        $res->assertRedirect(TestUtilities::$login_path);
    }

    public function testDeleteCommentWithAllValidData()
    {
        $user = User::factory()->create([
            "profile_picture" => TestUtilities::$validProfilePicture,
        ]);
        Auth::loginUsingId($user["id"]);
        // $user = Auth::user();
        //        $headers = [
        //            "HTTP_X-Requested-With" => "XMLHttpRequest",
        //            "X-CSRF-TOKEN" => csrf_token(),
        //        ];
        $comment = Comment::factory()->create([
            "user_id" => $user["id"],
        ]);
        $res = $this->delete("/video/comment?id=" . $comment["id"]);
        $res->assertSee("Successfully deleted");
        $comment = DB::table("comments")
            ->whereRaw("id = " . $comment["id"])
            ->first();
        $this->assertEquals(null, $comment);
    }

    public function testDeleteCommentWhenNoIDPassedIn()
    {
        $user = User::factory()->create([
            "profile_picture" => TestUtilities::$validProfilePicture,
        ]);
        Auth::loginUsingId($user["id"]);
        //        $user = Auth::user();
        //        $headers = [
        //            "HTTP_X-Requested-With" => "XMLHttpRequest",
        //            "X-CSRF-TOKEN" => csrf_token(),
        //        ];
        Comment::factory()->create([
            "user_id" => $user["id"],
        ]);
        $res = $this->delete(TestUtilities::$video_comment_path);
        $res->assertSee("Failed to delete. Id must be provided");
    }

    public function testDeleteCommentOnlyDeletedWhenIsUsers()
    {
        $users = User::factory()
            ->count(2)
            ->create([
                "profile_picture" => TestUtilities::$validProfilePicture,
            ]);
        Auth::loginUsingId($users[0]["id"]);
        Comment::factory()->create([
            "user_id" => $users[0]["id"],
        ]);
        $comment2 = Comment::factory()->create([
            "user_id" => $users[1]["id"],
        ]);
        $res = $this->delete("/video/comment?id=" . $comment2["id"]);
        $res->assertSee("Not allowed to delete other peoples comments");
        $comment2 = DB::table("comments")
            ->whereRaw("id = " . $comment2["id"])
            ->first();
        $this->assertEquals(false, $comment2 === null);
    }

    public function testPostCommentWithAuth()
    {
        // Auth myself
        $user = User::factory()->create([
            "profile_picture" => TestUtilities::$validProfilePicture,
        ]);
        Auth::loginUsingId($user["id"]);

        //
        // INCORRECT REQUEST
        //

        // No comment but with video title to test validation
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->post(
            TestUtilities::$video_comment_path,
            ["videoPostedOn" => $this->something_more_title],
            $headers
        );
        $res->assertStatus(406);
        $res->assertJson([
            "success" => false,
            "message" => "The comment field is required.",
        ]);

        // No date posted but with video title to test validation
        $data = ["comment" => "hello"];
        $data["videoPostedOn"] = $this->something_more_title;
        $res = $this->post(TestUtilities::$video_comment_path, $data, $headers);
        $res->assertStatus(406);
        $res->assertJson([
            "success" => false,
            "message" => "The date posted field is required.",
        ]);

        // No video posted on
        $data["datePosted"] = "2020-03-02";
        $data["videoPostedOn"] = null;
        $res = $this->post(TestUtilities::$video_comment_path, $data, $headers);
        $res->assertStatus(403);
        $res->assertJson([
            "success" => false,
            "message" => 'Some data wasn\'t provided',
        ]);

        // No video found with that title
        $data["videoPostedOn"] = "I Dont Exist";
        $res = $this->post(TestUtilities::$video_comment_path, $data, $headers);
        $res->assertStatus(404);
        $res->assertJson([
            "success" => false,
            "message" => "Video does not exist",
        ]);

        //
        // CORRECT REQUEST
        //

        // Run request with correct data
        $data["videoPostedOn"] = $this->something_more_title;
        $res = $this->post(TestUtilities::$video_comment_path, $data, $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true]);
    }

    public function testAutocomplete()
    {
        // Run request without auth
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->get("/video/titles?title=Hello", $headers);
        $res->assertStatus(302);

        // Auth myself
        $user = User::factory()->create();
        Auth::loginUsingId($user["id"]);

        // send requestResponds with empty array when no title is passed in e.g. no value in search bar
        $res = $this->get("/video/titles?title=", $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true, "data" => []]);

        // Send request with correct titles and Finds titles based on it
        $res = $this->get("/video/titles?title=S", $headers); // should match lava sample and something more
        $res->assertStatus(200);
        $res->assertJson([
            "success" => true,
            "data" => [$this->something_more_title, $this->lava_sample_title],
        ]);

        // make request with an incorrect title that will respond with no data
        $res = $this->get("/video/titles?title=I Dont exist", $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true, "data" => []]);
    }

    public function testWatchSingleVideoWithoutAuth()
    {
        Auth::logout();
        $response = $this->get(TestUtilities::$video_path);
        $response->assertStatus(302);
        $response = $this->get("/");
        $response->assertStatus(302);
    }

    public function testWatchSingleVideoWithAuthWithInvalidQuery()
    {
        $user = User::factory()->create([
            "logged_in" => 0,
        ]);
        Auth::loginUsingId($user["id"]);
        // make request with title but doesnt exist
        $response = $this->get("/video?requestedVideo=Idontexist");
        $response->assertStatus(404);
    }

    public function testWatchSingleVideoWithAuthWithNoQuery()
    {
        Cache::flush();
        // create user
        $user = User::factory()->create(["logged_in" => 0]);
        // Auth user
        Auth::loginUsingId($user["id"]);
        // Make request with no video request
        $response = $this->get(TestUtilities::$video_path);
        // Assert the view
        //$response->assertViewIs('Home');
        // Assert the status
        $response->assertStatus(403);
        // assert the data sent back to view
        //        $content = $response->getOriginalContent();
        //        $data = $content->getData();
        //        $this->assertEquals('Home', $data['title']); // defaults to something more
        //        $this->assertEquals('TestUsername', $data['username']);
        //        $this->assertEquals('Something More', $data['mainVideo']->title);
        //        $this->assertEquals('2', sizeof($data['rabbitHoleVideos']));
        //        // Shouldn't be the main video
        //        foreach ($data['rabbitHoleVideos'] as $vid) {
        //            $this->assertEquals(true, $vid->title !== 'Something More');
        //        }
        //        $this->assertEquals(9, sizeof($data['comments']));
        //        foreach ($data['comments'] as $comment) {
        //            $this->assertEquals(true, $comment->video_posted_on === 'Something More');
        //        }
        //        $this->assertEquals(true, $data['email'] === 'TestEmail@hotmail.com');
        //        TestUtilities::removeTestUsersInDb();
    }

    public function testWatchSingleVideoWithAuthWithQuery()
    {
        Cache::flush();
        $user = User::factory()->create();
        Auth::loginUsingId($user["id"]);
        // make request with correct title
        $response = $this->get(
            "/video?requestedVideo=$this->lava_sample_title"
        );
        // Assert the view
        $response->assertViewIs("watch");
        // Assert the status
        $response->assertStatus(200);
        // assert the data sent back to view
        $content = $response->getOriginalContent();
        $data = $content->getData();
        $this->assertEquals($this->lava_sample_title, $data["title"]); // defaults to something more
        $this->assertEquals(
            $this->lava_sample_title,
            $data["mainVideo"]->title
        );
        $this->assertEquals("2", sizeof($data["rabbitHoleVideos"]));
        // Shouldn't be the main video
        foreach ($data["rabbitHoleVideos"] as $vid) {
            $this->assertEquals(true, $vid->title !== $this->lava_sample_title);
        }
        $this->assertEquals(1, sizeof($data["comments"]));
        foreach ($data["comments"] as $comment) {
            $this->assertEquals(
                true,
                $comment->video_id === $this->lava_sample_id
            );
        }
    }

    public function testUpdateCommentWithNoAuth()
    {
        Cache::flush();
        // make request with correct title
        $response = $this->put(TestUtilities::$video_comment_path);
        $response->assertRedirect(TestUtilities::$login_path);
    }

    public function testUpdateCommentWithInvalidBody()
    {
        Cache::flush();
        $user = User::factory()->create();
        Comment::factory()->create([
            "user_id" => $user["id"],
        ]);
        Auth::loginUsingId($user["id"]);
        // make request with correct title
        $response = $this->put(TestUtilities::$video_comment_path);
        $response->assertSee(
            "Failed to delete. The id and text must be provided"
        );
    }

    public function testUpdateCommentWhenCommentToUpdateIsntUsers()
    {
        Cache::flush();
        $users = User::factory()
            ->count(2)
            ->create();
        $comment = Comment::factory()->create([
            "user_id" => $users[1]["id"],
        ]);
        Auth::loginUsingId($users[0]["id"]);
        // make request with correct title
        $response = $this->put(TestUtilities::$video_comment_path, [
            "id" => $comment["id"],
            "newComment" => "Hello world :)",
        ]);
        $response->assertSee(
            "Unauthenticated. Not allowed to delete other peoples comments"
        );
    }

    public function testUpdateCommentWhenAllDataIsCorrect()
    {
        Cache::flush();
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            "user_id" => $user["id"],
        ]);
        $commentId = $comment["id"];
        Auth::loginUsingId($user["id"]);
        // make request with correct title
        $newComment = "Hello world :)";
        $response = $this->put(TestUtilities::$video_comment_path, [
            "id" => $commentId,
            "newComment" => $newComment,
        ]);
        $response->assertJson([
            "success" => true,
            "message" => "Successfully updated",
        ]);
        $comment = Comment::where("id", $commentId)->first();
        $this->assertEquals($comment->comment, $newComment);
    }
}
