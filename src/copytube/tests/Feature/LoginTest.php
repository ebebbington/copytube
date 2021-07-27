<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UserModel;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    // TODO Is this type of fn reused everywhere?
    private function makePostRequest(
        string $email,
        string $password
    ): TestResponse {
        $data = [
            "email" => $email,
            "password" => $password,
        ];
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
        $user = UserModel::factory()->create();
        Auth::loginUsingId($user["id"]);
        $response = $this->json("GET", TestUtilities::$login_path);
        $response->assertRedirect("/home");
    }

    public function testPostLockedAccount()
    {
        Cache::flush();
        $user = UserModel::factory()->create([
            "login_attempts" => 0,
        ]);
        // Send post request
        $response = $this->makePostRequest(
            $user["email_address"],
            $user["email_address"]
        );
        $response->assertJson([
            "success" => false,
            "message" => "This account has been locked.",
        ]);
        $response->assertStatus(403);
    }

    public function testPostIncorrectPasswordButValidEmail()
    {
        $user = UserModel::factory()->create();
        $response = $this->makePostRequest(
            $user["email_address"],
            TestUtilities::$invalidPasswords[0]
        );
        $response->assertJson([
            "success" => false,
            "message" => "Failed to authenticate",
        ]);
        $response->assertStatus(403);
    }

    // eg cannot auth attempt it as the user isnt in db
    public function testPostNonExistingUser()
    {
        $response = $this->makePostRequest(
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $response->assertJson([
            "success" => false,
            "message" => "This account does not exist with that email",
        ]);
        $response->assertStatus(403);
    }

    public function testPostWhenAlreadyLoggedIn()
    {
        $user = UserModel::factory()->create();
        Auth::loginUsingId($user["id"]);
        $response = $this->makePostRequest(
            $user["email_address"],
            $user["password"]
        );
        $response->assertRedirect("/home");
    }

    public function testPostSuccessfulLogin()
    {
        $pass = "Welcome1";
        $user = UserModel::factory()->create([
            "email_address" => "ed@hotmail.com",
            "password" => Hash::make($pass),
        ]);
        $response = $this->makePostRequest($user["email_address"], $pass);
        $response->assertJson(["success" => true]);
        $response->assertStatus(200);
        $user = DB::table("users")
            ->where("email_address", "=", $user["email_address"])
            ->first();
        $this->assertEquals(0, $user->logged_in);
    }
}
