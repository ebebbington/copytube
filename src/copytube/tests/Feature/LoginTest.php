<?php

namespace Tests\Feature;

use App\BaseModel;
use App\User;
use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    private function makePostRequest($email, $password): TestResponse
    {
        $data = [];
        if (isset($email)) {
            $data["email"] = $email;
        }
        if (isset($password)) {
            $data["password"] = $password;
        }

        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        // Send the request
        return $this->post(TestUtilities::$login_path, $data, $headers);
    }

    public function testGetRequestWhenNotAuthed()
    {
        $response = $this->json("GET", TestUtilities::$login_path);
        $response->assertStatus(200);
        $response->assertViewIs("login");
    }

    public function testGetWhenAlreadyLoggedIn()
    {
        $userId = TestUtilities::createTestUserInDb();
        Auth::loginUsingId($userId);
        $response = $this->json("GET", TestUtilities::$login_path);
        $response->assertRedirect("/home");
    }

    public function testPostLockedAccount()
    {
        Cache::flush();
        TestUtilities::createTestUserInDb(["login_attempts" => 0]);
        // Send post request
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $response->assertJson(
            [
            "success" => false,
            "message" => "This account has been locked.",
            ]
        );
        $response->assertStatus(403);
    }

    public function testPostIncorrectPasswordButValidEmail()
    {
        TestUtilities::createTestUserInDb();
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$invalidPasswords[0]
        );
        $response->assertJson(
            [
            "success" => false,
            "message" => "Failed to authenticate",
            ]
        );
        $response->assertStatus(403);
    }

    // eg cannot auth attempt it as the user isnt in db
    public function testPostNonExistingUser()
    {
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $response->assertJson(
            [
            "success" => false,
            "message" => "This account does not exist with that email",
            ]
        );
        $response->assertStatus(403);
    }

    public function testPostWhenAlreadyLoggedIn()
    {
        $userId = TestUtilities::createTestUserInDb();
        Auth::loginUsingId($userId);
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $response->assertRedirect("/home");
    }

    public function testPostSuccessfulLogin()
    {
        TestUtilities::createTestUserInDb();
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $user = DB::table("users")
            ->where("email_address", "=", TestUtilities::$validEmail)
            ->first();
        $this->assertEquals(0, $user->logged_in);
        $response->assertJson(["success" => true]);
        $response->assertStatus(200);
    }
}
