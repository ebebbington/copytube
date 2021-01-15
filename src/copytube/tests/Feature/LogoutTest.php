<?php

namespace Tests\Feature;

use App\User;
use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    private function makeDeleteRequest(): ?object
    {
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        // Send the request
        $response = $this->json("GET", "/logout");
        return $response;
    }

    public function testDeleteUserWithAuth()
    {
        // create user
        TestUtilities::createTestUserInDb();
        $user = TestUtilities::getTestUserInDb();
        // Auth user
        Auth::loginUsingId($user->id);
        // User must be authed
        $authedUser = Auth::user();
        $this->assertEquals(true, isset($authedUser));
        // send request
        $response = $this->makeDeleteRequest();
        // assert response
        $response->assertStatus(302);
        // user must have logged_in = 1
        $user = TestUtilities::getTestUserInDb();
        $this->assertEquals(1, $user->logged_in);
        Auth::logout();
    }

    public function testDeleteUserWithoutAuth()
    {
        // run request without logging in
        $response = $this->makeDeleteRequest();
        $response->assertStatus(401);
    }
}
