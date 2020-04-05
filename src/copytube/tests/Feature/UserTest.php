<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    private function createUserAndGetId ()
    {
        DB::table('users')->insert([
            'username' => 'TestUsername',
            'email_address' => 'TestEmail@hotmail.com',
            'password' => UserModel::generateHash('TestPassword1'),
            'login_attempts' => 3,
            'logged_in' => 0
        ]);
        $userId = DB::table('users')->select('id')->where('email_address', '=', 'TestEmail@hotmail.com')->first();
        $userId = $userId->id;
        return $userId;
    }
    public function testDeleteMethodWithoutAuth ()
    {
        // Test request when not authed - should fail
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->delete('/user', $headers);
        $res->assertStatus(302);
    }
    public function testDeleteMethodWithAuthOnSuccess ()
    {
        // Copy profile picture
        $userId = $this->createUserAndGetId();
        $profilePicPath = 'img/'.$userId.'/test.jpg';
        Storage::disk('local_public')->copy('img/sample.jpg', $profilePicPath);
        // Update user in table
        DB::table('users')
            ->where('email_address', '=', 'TestEmail@hotmail.com')
            ->update(['profile_picture' => $profilePicPath]);

        // Auth myself as its an authed route
        $user = DB::table('users')->where('email_address', '=', 'TestEmail@hotmail.com')->first();
        Auth::loginUsingId($user->id);
        $user = Auth::user();

        // Add comments before
        DB::table('comments')->insert([
            [
                'comment' => 'Hello',
                'author' => 'hello',
                'user_id' => $user->id,
                'video_posted_on' => 'test',
                'date_posted' => '2020-03-03'
            ],
            [
                'comment' => 'Hello',
                'author' => 'hello',
                'user_id' => $user->id,
                'video_posted_on' => 'test',
                'date_posted' => '2020-03-03'
            ]
        ]);

        // Make request
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->delete('/user', $headers);

        // Check picture was removed
        //Storage::disk('local_public')->assertMissing($profilePicPath);

        // Check row was deleted
        $row = DB::table('users')->where('email_address', '=', 'TestEmail@hotmail.com')->first();
        $this->assertEquals(false, $row);

        // Check all comments were deleted
        $comments = DB::table('comments')->where('user_id', '=', $user->id)->get();
        $this->assertEquals(false, sizeof($comments) >= 1);

        // Check we are no longer authed
        $user = Auth::user();
        $this->assertEquals(false, $user);

        // Check we were redirected (302) to register page
        $res->assertStatus(302);
    }

    /**
     * To test when deleteting the directory fails
     */
    public function testDeleteMethodWithAuthOnError ()
    {
        $userId = $this->createUserAndGetId();
        // Auth myself as its an authed route
        Auth::loginUsingId($userId);
        $user = Auth::user();
        // Make request
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        $res = $this->delete('/user', $headers);
    }
}
