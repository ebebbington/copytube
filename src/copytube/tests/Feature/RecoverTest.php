<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecoverTest extends TestCase
{
    private $uri = "/recover";

    private function sendPostRequest($email, $password)
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
        TestUtilities::createTestUserInDb(["recover_token" => "test_token"]);
        $response = $this->get("/recover?token=test_token");
        $response->assertStatus(200);
        $response->assertViewIs("recover");
        $response->assertCookie("recoverToken");
        TestUtilities::removeTestUsersInDb();
    }

    public function testPostWhenTokenDoesntMatch()
    {
        TestUtilities::createTestUserInDb(["recover_token" => "test_token"]);
        $response = $this->withCookie("recoverToken", "goody")->post(
            $this->uri,
            [
                "email" => TestUtilities::$validEmail,
                "password" => TestUtilities::$validPassword,
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        TestUtilities::removeTestUsersInDb();
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
        TestUtilities::createTestUserInDb(["recover_token" => $tokenValue]);
        $response = $this->withCookie("recoverToken", $tokenValue)->post(
            $this->uri,
            [
                "email" => TestUtilities::$validEmail,
                "password" => "a",
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        TestUtilities::removeTestUsersInDb();
        $response->assertJson([
            "success" => false,
            "message" => "The password format is invalid.",
        ]);
        $response->assertStatus(403);
    }

    public function testPost()
    {
        // Test when getting user that doesnt exist
        $response = $this->sendPostRequest("idontexist@hotmail.com", "");
        $response->assertJson([
            "success" => false,
            "message" => "Unable to authenticate",
        ]);
        $response->assertStatus(403);

        // Test it updates the row correctly
        TestUtilities::removeTestUsersInDb();
        $this->disableCookieEncryption();
        TestUtilities::createTestUserInDb([
            "recover_token" => "test_token",
            "profile_picture" => "Test.png",
            "login_attempts" => 0,
        ]);
        $response = $this->withCookie("recoverToken", "test_token")->post(
            $this->uri,
            [
                "email" => TestUtilities::$validEmail,
                "password" => TestUtilities::$validPassword,
            ],
            [
                "HTTP_X-Requested-With" => "XMLHttpRequest",
                "X-CSRF-TOKEN" => csrf_token(),
            ]
        );
        $user = TestUtilities::getTestUserInDb();
        $this->assertEquals(true, $user->login_attempts === 3);
        $this->assertEquals(
            true,
            Hash::check(TestUtilities::$validPassword, $user->password)
        );

        // Assert json response
        $response->assertJson([
            "success" => true,
            "message" => "Successfully updated your password",
        ]);

        $this->assertEquals(true, $user->recover_token === null);

        TestUtilities::removeTestUsersInDb();
    }
}
