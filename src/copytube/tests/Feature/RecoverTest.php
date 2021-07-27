<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UserModel;

class RecoverTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private $uri = "/recover";

    private function sendPostRequest(string $email, string $password)
    {
        $data = [
            "email" => $email,
            "password" => $password,
        ];
        $headers = [
            "HTTP_X-Requested-With" => "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        // Send the request
        return $this->post($this->uri, $data, $headers); // the response
    }

    public function testGetWithIncorrectToken()
    {
        // No query
        $response = $this->get($this->uri);
        $response->assertStatus(302);
        $response->assertRedirect("login");

        // No value
        $response = $this->get("/recover?token=");
        $response->assertStatus(302);
        $response->assertRedirect("login");

        // Incorrect value
        $response = $this->get("/recover?token=Idontexist");
        $response->assertStatus(302);
        $response->assertRedirect("login");
    }

    public function testGetWithCorrectToken()
    {
        // Assert correct response with correct token
        UserModel::factory()->create([
            "recover_token" => "test_token",
        ]);
        $response = $this->get("/recover?token=test_token");
        $response->assertStatus(200);
        $response->assertViewIs("recover");
        $response->assertCookie("recoverToken");
    }

    public function testPostWhenTokenDoesntMatch()
    {
        $user = UserModel::factory()->create([
            "recover_token" => "test_token",
        ]);
        $response = $this->withCookie("recoverToken", "goody")->post(
            $this->uri,
            [
                "email" => $user["email_address"],
                "password" => TestUtilities::$validPassword,
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        $response->assertJson([
            "success" => false,
            "message" => "Token does not match",
        ]);
        $response->assertStatus(403);
    }

    public function testPostWhenCredsFailValidation()
    {
        $tokenValue = "test_token";
        $this->disableCookieEncryption();
        $user = UserModel::factory()->create([
            "recover_token" => $tokenValue,
        ]);
        $response = $this->withCookie("recoverToken", $tokenValue)->post(
            $this->uri,
            [
                "email" => $user["email_address"],
                "password" => "a",
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        $response->assertJson([
            "success" => false,
            "message" => "The password format is invalid.",
        ]);
        $response->assertStatus(403);
    }

    public function testPostWhenUserDoesntExist()
    {
        // Test when getting user that doesnt exist
        $response = $this->sendPostRequest("idontexist@hotmail.com", "");
        $response->assertJson([
            "success" => false,
            "message" => "Unable to authenticate",
        ]);
        $response->assertStatus(403);
    }

    public function testPost()
    {
        // Test it updates the row correctly
        $this->disableCookieEncryption();
        $user = UserModel::factory()->create([
            "recover_token" => "test_token",
            "profile_picture" => "Test.png",
            "login_attempts" => 0,
        ]);
        $response = $this->withCookie("recoverToken", "test_token")->post(
            $this->uri,
            [
                "email" => $user["email_address"],
                "password" => "Welcome2",
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        $user = UserModel::where(
            "email_address",
            $user["email_address"]
        )->first();
        $this->assertEquals(true, $user["login_attempts"] === 3);
        $this->assertEquals(true, Hash::check("Welcome2", $user["password"]));

        // Assert json response
        $response->assertJson([
            "success" => true,
            "message" => "Successfully updated your password",
        ]);

        $this->assertEquals(true, $user->recover_token === null);
    }
}
