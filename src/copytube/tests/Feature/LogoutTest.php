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

    private function makeDeleteRequest (): ?Object
    {
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        // Send the request
        $response = $this->json('GET', '/logout');
        return $response;
    }

    public function testDeleteUserWithAuth ()
    {
        // create user
        DB::table('users')->insert([
            'username' => 'TestUsername',
            'email_address' => 'TestEmail@hotmail.com',
            'password' => UserModel::generateHash('TestPassword1'),
            'login_attempts' => 3,
            'logged_in' => 0
        ]);
        $user = DB::table('users')->where('email_address', '=', 'TestEmail@hotmail.com')->first();
        // Auth user
        Auth::loginUsingId($user->id);
        // User must be authed
        $user = Auth::user();
        $this->assertEquals(true, isset($user));
        // send request
        $response = $this->makeDeleteRequest();
        // assert response
        $response->assertStatus(302);
        // user must have logged_in = 1
        $user = DB::table('users')->where('email_address', '=', 'TestEmail@hotmail.com')->first();
        $this->assertEquals(1, $user->logged_in);
        Auth::logout();
    }

    public function testDeleteUserWithoutAuth ()
    {
        // run request without logging in
        $response = $this->makeDeleteRequest();
        $response->assertStatus(401);
    }

}
