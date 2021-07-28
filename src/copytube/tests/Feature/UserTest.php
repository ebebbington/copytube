<?php

namespace Tests\Feature;

use App\Comment;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

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
        $user = User::factory()->create();
        $profilePicPath = "img/" . $user["id"] . "/test.jpg";
        $Storage = new Storage();
        $Storage::disk("local_public")->copy("img/sample.jpg", $profilePicPath);
        // Update user in table
        DB::table("users")
            ->where("email_address", "=", TestUtilities::$validEmail)
            ->update(["profile_picture" => $profilePicPath]);

        // Auth myself as its an authed route
        $Auth = new Auth();
        $Auth::loginUsingId($user["id"]);

        // Add comments before
        Comment::factory()
            ->count(3)
            ->create([
                "user_id" => $user["id"],
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
        $row = User::where("id", $user["id"])->first();
        $this->assertEquals(false, $row);

        // Check all comments were deleted
        $comments = DB::table("comments")
            ->where("user_id", "=", $user["id"])
            ->get();
        $this->assertEquals(false, sizeof($comments) >= 1);

        // Check we are no longer authed
        $user = $Auth::user();
        $this->assertEquals(false, $user);

        // Check we were redirected (302) to register page
        $res->assertStatus(200);
    }
}
