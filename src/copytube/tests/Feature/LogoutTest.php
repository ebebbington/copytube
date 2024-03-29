<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function makeDeleteRequest(): ?object
    {
        //        $headers = [
        //            "HTTP_X-Requested-With" => "XMLHttpRequest",
        //            "X-CSRF-TOKEN" => csrf_token(),
        //        ];
        // Send the request
        return $this->json("GET", "/logout"); // $response
    }

    public function testDeleteUserWithAuth()
    {
        // create user
        $user = User::factory()->create([
            "logged_in" => 0,
        ]);
        // Auth user
        Auth::loginUsingId($user["id"]);
        // User must be authed
        $authedUser = Auth::user();
        $this->assertEquals(true, isset($authedUser));
        // send request
        $response = $this->makeDeleteRequest();
        // assert response
        $response->assertStatus(302);
        // user must have logged_in = 1
        $user = User::where("email_address", $user["email_address"])->first();
        $this->assertEquals(1, $user["logged_in"]);
        Auth::logout();
    }

    public function testDeleteUserWithoutAuth()
    {
        // run request without logging in
        $response = $this->makeDeleteRequest();
        $response->assertStatus(401);
    }
}
