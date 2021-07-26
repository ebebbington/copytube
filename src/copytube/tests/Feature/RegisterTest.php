<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Hash;
use Storage;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    private string $password_invalid_error_msg = "The password format is invalid.";
    private string $profile_picture_invalid_error_msg = "The profile picture format is invalid.";

    private function makePostRequest(
        $username,
        $email,
        $password,
        $profilePicture = null,
        $noCsrf = null
    ): ?object {
        $data = [];
        if (isset($username)) {
            $data["username"] = $username;
        }
        if (isset($email)) {
            $data["email"] = $email;
        }
        if (isset($password)) {
            $data["password"] = $password;
        }
        if (isset($profilePicture)) {
            $data["profile-picture"] = $profilePicture;
        }

        $headers = [
            "HTTP_X-Requested-With" => isset($noCsrf) ? "" : "XMLHttpRequest",
            "X-CSRF-TOKEN" => csrf_token(),
        ];
        // Send the request
        return $this->post("/register", $data, $headers); // response
    }

    public function testGetRequest()
    {
        $response = $this->json("GET", "/register");
        $response->assertStatus(200);
        $response->assertViewIs("register");
    }

    public function testPostRequestWhenNotSelectingProfileImage()
    {
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            TestUtilities::$validPassword
        );
        $response->assertJson([
            "success" => true,
        ]);
        $response->assertStatus(200);
        $user = TestUtilities::getTestUserInDb();
        TestUtilities::removeTestUsersInDb();
        $username = $user->username;
        $path = $user->profile_picture;
        $email = $user->email_address;
        $this->assertEquals(TestUtilities::$validUsername, $username);
        $this->assertEquals(TestUtilities::$validEmail, $email);
        $this->assertEquals("img/sample.jpg", $path);
    }

    public function testFailedPostValidation()
    {
        //
        // USERNAME
        //

        // Empty
        $response = $this->makePostRequest(
            "",
            TestUtilities::$validEmail,
            TestUtilities::$validPassword,
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => "The username field is required.",
        ]);
        $response->assertStatus(401);

        //
        // EMAIL
        //

        // Empty
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            "",
            TestUtilities::$validPassword,
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => "The email field is required.",
        ]);
        $response->assertStatus(401);
        // Not an email
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            "edward",
            TestUtilities::$validPassword,
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => "The email must be a valid email address.",
        ]);
        $response->assertStatus(401);

        //
        // PASSWORD
        //

        // Empty
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => "The password field is required.",
        ]);
        $response->assertStatus(401);
        // No number
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "HelloWorld",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->password_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // Not correct length
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "7charss",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->password_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // No letters
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "11111111",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->password_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // No uppercase character
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "welcome1",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->password_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // No lowercase character
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            "WELCOME1",
            UploadedFile::fake()->image(TestUtilities::$validProfilePicture)
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->password_invalid_error_msg,
        ]);
        $response->assertStatus(401);

        //
        // PROFILE PICTURE
        //

        // xlsx
        $file = UploadedFile::fake()->create("test.xlsx");
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            TestUtilities::$validPassword,
            $file
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->profile_picture_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // docx
        $file = UploadedFile::fake()->create("test.docx");
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            TestUtilities::$validPassword,
            $file
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->profile_picture_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // pdf
        $file = UploadedFile::fake()->create("test.pdf");
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            TestUtilities::$validPassword,
            $file
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->profile_picture_invalid_error_msg,
        ]);
        $response->assertStatus(401);
        // gif
        $file = UploadedFile::fake()->create("test.gif");
        $response = $this->makePostRequest(
            TestUtilities::$validUsername,
            TestUtilities::$validEmail,
            TestUtilities::$validPassword,
            $file
        );
        $response->assertJson([
            "success" => false,
            "message" => $this->profile_picture_invalid_error_msg,
        ]);
        $response->assertStatus(401);
    }

    public function testProfilePictureIsSavedOnPost()
    {
        $TestUtilities = new TestUtilities();
        $TestUtilities::removeTestUsersInDb();

        $Storage = new Storage();
        $Storage::fake("local");
        $this->makePostRequest(
            $TestUtilities::$validUsername,
            $TestUtilities::$validEmail,
            $TestUtilities::$validPassword,
            UploadedFile::fake()->image("img/something_more.jpg")
        );
        // Assert the file was stored...
        $user = $TestUtilities::getTestUserInDb();
        $picPath = str_replace("img/", "", $user->profile_picture);
        $Storage::disk("local")->assertExists($picPath);

        $TestUtilities::removeTestUsersInDb();
    }

    public function testSuccessfulPostRequest()
    {
        $TestUtilities = new TestUtilities();
        // First remove the test user if there is one
        $TestUtilities::removeTestUsersInDb();

        // Test adding a user and that table has that column
        $response = $this->makePostRequest(
            $TestUtilities::$validUsername,
            $TestUtilities::$validEmail,
            $TestUtilities::$validPassword,
            UploadedFile::fake()->image($TestUtilities::$validProfilePicture)
        );

        // Get user from DB and assert the data
        $user = $TestUtilities::getTestUserInDb();
        $this->assertEquals($TestUtilities::$validUsername, $user->username);
        $this->assertEquals($TestUtilities::$validEmail, $user->email_address);
        $Hash = new Hash();
        $this->assertEquals(
            true,
            $Hash::check($TestUtilities::$validPassword, $user->password)
        );
        $this->assertEquals(true, isset($user->profile_picture));

        // Remove the data
        $TestUtilities::removeTestUsersInDb();

        // Assert the response
        $response->assertJson(["success" => true]);
        $response->assertStatus(200);
    }
}
