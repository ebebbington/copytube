<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Util\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    public function testDeleteMethodWithoutAuth()
    {
        // Test request when not authed - should fail
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->delete("/user", $headers);
        $res->assertStatus(302);
    }
    public function testDeleteMethodWithAuthOnSuccess()
    {
        // Copy profile picture
        $userId = TestUtilities::createTestUserInDb();
        $profilePicPath = "img/" . $userId . "/test.jpg";
        $Storage = new Storage();
        $Storage::disk("local_public")->copy("img/sample.jpg", $profilePicPath);
        // Update user in table
        $Database = new DB();
        $TestUtilities = new TestUtilities();
        $Database
            ::table("users")
            ->where("email_address", "=", $TestUtilities::$validEmail)
            ->update(["profile_picture" => $profilePicPath]);

        // Auth myself as its an authed route
        $Auth = new Auth();
        $Auth::loginUsingId($userId);

        // Add comments before
        $Database::table("comments")->insert([
            [
                "comment" => "Hello",
                "author" => "hello",
                "user_id" => $userId,
                "video_posted_on" => "test",
                "date_posted" => "2020-03-03",
            ],
            [
                "comment" => "Hello",
                "author" => "hello",
                "user_id" => $userId,
                "video_posted_on" => "test",
                "date_posted" => "2020-03-03",
            ],
        ]);

        // Make request
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        $res = $this->delete("/user", $headers);

        // Check picture was removed
        //Storage::disk('local_public')->assertMissing($profilePicPath);

        // Check row was deleted
        $row = $TestUtilities::getTestUserInDb();
        $this->assertEquals(false, $row);

        // Check all comments were deleted
        $comments = $Database
            ::table("comments")
            ->where("user_id", "=", $userId)
            ->get();
        $this->assertEquals(false, sizeof($comments) >= 1);

        // Check we are no longer authed
        $user = $Auth::user();
        $this->assertEquals(false, $user);

        // Check we were redirected (302) to register page
        $res->assertStatus(200);
    }
}
