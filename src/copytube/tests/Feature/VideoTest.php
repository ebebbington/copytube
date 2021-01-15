<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    public function testPostCommentWithoutAuth()
    {
        TestUtilities::removeTestUsersInDb();
        // Run request without auth
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->post("/video/comment", ["comment" => "test"], $headers);
        $res->assertStatus(302);
        $res->assertRedirect("/login");
    }

    public function testDeleteCommentWithoutAuth()
    {
        $res = $this->delete("/video/comment?id=12345");
        $res->assertStatus(302);
        $res->assertRedirect("/login");
    }

    public function testDeleteCommentWithAllValidData()
    {
        TestUtilities::removeTestUsersInDb();
        $id = TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
        ]);
        Auth::loginUsingId($id);
        $user = Auth::user();
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $user = Auth::user();
        $commentId = TestUtilities::createTestCommentInDb($user);
        $res = $this->delete("/video/comment?id=" . $commentId);
        $res->assertSee("Successfully deleted");
        $comment = DB::table("comments")
            ->whereRaw("id = $commentId")
            ->first();
        $this->assertEquals(null, $comment);
        TestUtilities::removeTestUsersInDb();
        DB::table("comments")
            ->whereRaw("id = $commentId")
            ->delete();
    }

    public function testDeleteCommentWhenNoIDPassedIn()
    {
        TestUtilities::removeTestUsersInDb();
        $id = TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
        ]);
        Auth::loginUsingId($id);
        $user = Auth::user();
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $user = Auth::user();
        $commentId = TestUtilities::createTestCommentInDb($user);
        $res = $this->delete("/video/comment");
        $res->assertSee("Failed to delete. Id must be provided");
    }

    public function testDeleteCommentOnlyDeletedWhenIsUsers()
    {
        TestUtilities::removeTestUsersInDb();
        $userId1 = TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
        ]);
        Auth::loginUsingId($userId1);
        $userId2 = TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
        ]);
        $user2 = TestUtilities::getTestUserInDb($userId2);
        $user1 = Auth::user();
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $commentId1 = TestUtilities::createTestCommentInDb($user1);
        $commentId2 = TestUtilities::createTestCommentInDb($user2);
        $res = $this->delete("/video/comment?id=" . $commentId2);
        $res->assertSee("Not allowed to delete other peoples comments");
        $comment2 = DB::table("comments")
            ->whereRaw("id = $commentId2")
            ->first();
        $this->assertEquals(false, $comment2 === null);
        TestUtilities::removeTestUsersInDb();
        DB::table("comments")
            ->whereRaw("id = $commentId1")
            ->delete();
        DB::table("comments")
            ->whereRaw("id = $commentId2")
            ->delete();
    }

    public function testPostCommentWithAuth()
    {
        // Auth myself
        $id = TestUtilities::createTestUserInDb([
            "profile_picture" => "img/sample.jpg",
        ]);
        Auth::loginUsingId($id);
        $user = Auth::user();

        //
        // INCORRECT REQUEST
        //

        // No comment but with video title to test validation
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->post(
            "/video/comment",
            ["videoPostedOn" => "Something More"],
            $headers
        );
        $res->assertStatus(406);
        $res->assertJson([
            "success" => false,
            "message" => "The comment field is required.",
        ]);

        // No date posted but with video title to test validation
        $data = ["comment" => "hello"];
        $data["videoPostedOn"] = "Something More";
        $res = $this->post("/video/comment", $data, $headers);
        $res->assertStatus(406);
        $res->assertJson([
            "success" => false,
            "message" => "The date posted field is required.",
        ]);

        // No video posted on
        $data["datePosted"] = "2020-03-02";
        $data["videoPostedOn"] = null;
        $res = $this->post("/video/comment", $data, $headers);
        $res->assertStatus(403);
        $res->assertJson([
            "success" => false,
            "message" => 'Some data wasn\'t provided',
        ]);

        // No video found with that title
        $data["videoPostedOn"] = "I Dont Exist";
        $res = $this->post("/video/comment", $data, $headers);
        $res->assertStatus(404);
        $res->assertJson([
            "success" => false,
            "message" => "Video does not exist",
        ]);

        //
        // CORRECT REQUEST
        //

        // Run request with correct data
        $data["videoPostedOn"] = "Something More";
        $res = $this->post("/video/comment", $data, $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true]);

        // Remove all comments and user
        DB::table("comments")
            ->where("user_id", "=", $id)
            ->delete();
        TestUtilities::removeTestUsersInDb();
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
        $id = TestUtilities::createTestUserInDb();
        Auth::loginUsingId($id);

        // send requestResponds with empty array when no title is passed in e.g. no value in search bar
        $res = $this->get("/video/titles?title=", $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true, "data" => []]);

        // Send request with correct titles and Finds titles based on it
        $res = $this->get("/video/titles?title=S", $headers); // should match lava sample and something more
        $res->assertStatus(200);
        $res->assertJson([
            "success" => true,
            "data" => ["Something More", "Lava Sample"],
        ]);

        // make request with an incorrect title that will respond with no data
        $res = $this->get("/video/titles?title=I Dont exist", $headers);
        $res->assertStatus(200);
        $res->assertJson(["success" => true, "data" => []]);

        TestUtilities::removeTestUsersInDb();
    }

    public function testWatchSingleVideoWithoutAuth()
    {
        Auth::logout();
        $response = $this->get("/video");
        $response->assertStatus(302);
        $response = $this->get("/");
        $response->assertStatus(302);
    }

    public function tesWatchSingleVideoWithAuthWithInvalidQuery()
    {
        $id = TestUtilities::createTestUserInDb(["logged_in" => 0]);
        TestUtilities::logUserIn($id);
        // make request with title but doesnt exist
        $response = $this->get("/video?requestedVideo=Idontexist");
        $response->assertStatus(404);
        TestUtilities::removeTestUsersInDb();
    }

    public function testWatchSingleVideoWithAuthWithNoQuery()
    {
        Cache::flush();
        // create user
        $id = TestUtilities::createTestUserInDb(["logged_in" => 0]);
        // Auth user
        TestUtilities::logUserIn($id);
        // Make request with no video request
        $response = $this->get("/video");
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
        $id = TestUtilities::createTestUserInDb();
        TestUtilities::logUserIn($id);
        // make request with correct title
        $response = $this->get("/video?requestedVideo=Lava Sample");
        // Assert the view
        $response->assertViewIs("watch");
        // Assert the status
        $response->assertStatus(200);
        // assert the data sent back to view
        $content = $response->getOriginalContent();
        $data = $content->getData();
        $this->assertEquals("Lava Sample", $data["title"]); // defaults to something more
        $this->assertEquals("TestUsername", $data["username"]);
        $this->assertEquals("Lava Sample", $data["mainVideo"]->title);
        $this->assertEquals("2", sizeof($data["rabbitHoleVideos"]));
        // Shouldn't be the main video
        foreach ($data["rabbitHoleVideos"] as $vid) {
            $this->assertEquals(true, $vid->title !== "Lava Sample");
        }
        $this->assertEquals(3, sizeof($data["comments"]));
        foreach ($data["comments"] as $comment) {
            $this->assertEquals(
                true,
                $comment->video_posted_on === "Lava Sample"
            );
        }
        $this->assertEquals(true, $data["email"] === "TestEmail@hotmail.com");
        TestUtilities::removeTestUsersInDb();
    }

    public function testUpdateCommentWithNoAuth()
    {
        Cache::flush();
        // make request with correct title
        $response = $this->put("/video/comment");
        $response->assertRedirect("/login");
    }

    public function testUpdateCommentWithInvalidBody()
    {
        Cache::flush();
        $userId = TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb($userId);
        $commentId = TestUtilities::createTestCommentInDb($user);
        $comment = DB::table("comments")
            ->where("id", "=", $commentId)
            ->first();
        TestUtilities::logUserIn($userId);
        // make request with correct title
        $response = $this->put("/video/comment");
        $response->assertSee(
            "Failed to delete. The id and text must be provided"
        );
        TestUtilities::removeTestUsersInDb();
        TestUtilities::removeTestCommentsInDB($commentId);
    }

    public function testUpdateCommentWhenCommentToUpdateIsntUsers()
    {
        Cache::flush();
        $userId1 = TestUtilities::createTestUserInDb();
        $userId2 = TestUtilities::createTestUserInDb();
        $user1 = TestUtilities::getTestUserInDb($userId1);
        $user2 = TestUtilities::getTestUserInDb($userId2);
        $commentId = TestUtilities::createTestCommentInDb($user1);
        $comment = DB::table("comments")
            ->where("id", "=", $commentId)
            ->first();
        TestUtilities::logUserIn($userId2);
        // make request with correct title
        $response = $this->put("/video/comment", [
            "id" => $commentId,
            "newComment" => "Hello world :)",
        ]);
        $response->assertSee(
            "Unauthenticated. Not allowed to delete other peoples comments"
        );
        TestUtilities::removeTestUsersInDb();
        TestUtilities::removeTestCommentsInDB($commentId);
    }

    public function testUpdateCommentWhenAllDataIsCorrect()
    {
        Cache::flush();
        $id = TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb($id);
        $commentId = TestUtilities::createTestCommentInDb($user);
        $comment = DB::table("comments")
            ->where("id", "=", $commentId)
            ->first();
        TestUtilities::logUserIn($id);
        // make request with correct title
        $response = $this->put("/video/comment", [
            "id" => $commentId,
            "newComment" => "Hello world :)",
        ]);
        $response->assertSee("Successfully updated");
        $updatedComment = DB::table("comments")
            ->where("id", "=", $commentId)
            ->first();
        $this->assertEquals($updatedComment->comment, "Hello world :)");
        TestUtilities::removeTestUsersInDb();
        TestUtilities::removeTestCommentsInDB($commentId);
    }
}
