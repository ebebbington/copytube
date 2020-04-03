<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\UserModel;

class RegisterTest extends TestCase
{
    private $validUsername = 'Mr Test Username';

    private $validEmail = 'mrtestemail@hotmail.com';

    private $validPassword = 'MrTestPa55word1';

    private $validProfilePicturePath = 'img/sample.jpg';

    /**
     * @test
     */
    public function newtest()
    {

    }

    /**
     * Remove test users from the users table
     *
     * @return void
     */
    private function removeTestUserFromDB(): void
    {
        // Remove the data
        DB::table('users')
            ->where('username', $this->validUsername)
            ->where('email_address', $this->validEmail)
            ->delete();
    }

    private function makePostRequest ($username, $email, $password, $profilePicture): ?Object
    {
        $data = [];
        if (isset($username)) $data['username'] = $username;
        if (isset($email)) $data['email'] = $email;
        if (isset($password)) $data['password'] = $password;
        if (isset($profilePicture)) $data['profile-picture'] = $profilePicture;

        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        // Send the request
        $response = $this->post('/register', $data, $headers);
        return $response;
    }

    public function testGetRequest()
    {
        $response = $this->json('GET', '/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

     public function testFailedPostValidation ()
     {
         //
         // USERNAME
         //

         // Empty
         $response = $this->makePostRequest(
            '',
             $this->validEmail,
             $this->validPassword,
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The username field is required.']);
         $response->assertStatus(401);

         //
         // EMAIL
         //

         // Empty
         $response = $this->makePostRequest(
             $this->validUsername,
             '',
             $this->validPassword,
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The email field is required.']);
         $response->assertStatus(401);
         // Not an email
         $response = $this->makePostRequest(
             $this->validUsername,
             'edward',
             $this->validPassword,
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The email must be a valid email address.']);
         $response->assertStatus(401);

         //
         // PASSWORD
         //

         // Empty
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             '',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password field is required.']);
         $response->assertStatus(401);
         // No number
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             'HelloWorld',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password format is invalid.']);
         $response->assertStatus(401);
         // Not correct length
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             '7charss',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password format is invalid.']);
         $response->assertStatus(401);
         // No letters
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             '11111111',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password format is invalid.']);
         $response->assertStatus(401);
         // No uppercase character
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             'welcome1',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password format is invalid.']);
         $response->assertStatus(401);
         // No lowercase character
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             'WELCOME1',
             UploadedFile::fake()->image($this->validProfilePicturePath)
         );
         $response->assertJson(['success' => false, 'message' => 'The password format is invalid.']);
         $response->assertStatus(401);

         //
         // PROFILE PICTURE
         //

         // xlsx
         $file = UploadedFile::fake()->create('test.xlsx');
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             $this->validPassword,
             $file
         );
         $response->assertJson(['success' => false, 'message' => 'The profile picture format is invalid.']);
         $response->assertStatus(401);
         // docx
         $file = UploadedFile::fake()->create('test.docx');
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             $this->validPassword,
             $file
         );
         $response->assertJson(['success' => false, 'message' => 'The profile picture format is invalid.']);
         $response->assertStatus(401);
         // pdf
         $file = UploadedFile::fake()->create('test.pdf');
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             $this->validPassword,
             $file
         );
         $response->assertJson(['success' => false, 'message' => 'The profile picture format is invalid.']);
         $response->assertStatus(401);
         // gif
         $file = UploadedFile::fake()->create('test.gif');
         $response = $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             $this->validPassword,
             $file
         );
         $response->assertJson(['success' => false, 'message' => 'The profile picture format is invalid.']);
         $response->assertStatus(401);
     }

     public function testPostWhenUserExists ()
     {
         $this->removeTestUserFromDB();
         $this->makePostRequest($this->validUsername, $this->validEmail, $this->validPassword, '');
         $response = $this->makePostRequest($this->validUsername, $this->validEmail, $this->validPassword, '');
         $response->assertJson(['success' => false, 'message' => 'user already exists']);
         $response->assertStatus(403);
         $this->removeTestUserFromDB();
     }

     public function testProfilePictureIsSavedOnPost ()
     {
         $this->removeTestUserFromDB();

         Storage::fake('local');
         $this->makePostRequest(
             $this->validUsername,
             $this->validEmail,
             $this->validPassword,
             UploadedFile::fake()->image('img/something_more.jpg')
         );
         // Assert the file was stored...
         $user = DB::table('users')
             ->whereRaw("username = '$this->validUsername'")
             ->first();
         $picPath = str_replace('img/', '', $user->profile_picture);
         Storage::disk('local')->assertExists($picPath);

         $this->removeTestUserFromDB();
     }

    public function testSuccessfulPostRequest()
    {
        // First remove the test user if there is one
        $this->removeTestUserFromDB();

        // Test adding a user and that table has that column
        $response = $this->makePostRequest(
            $this->validUsername,
            $this->validEmail,
            $this->validPassword,
            UploadedFile::fake()->image($this->validProfilePicturePath)
        );

        // Get user from DB and assert the data
        $user = DB::table('users')->whereRaw("email_address = '$this->validEmail'")->first();
        $this->assertEquals($this->validUsername, $user->username);
        $this->assertEquals($this->validEmail, $user->email_address);
        $this->assertEquals(true, Hash::check($this->validPassword, $user->password));
        $this->assertEquals(true, isset($user->profile_picture));

        // Remove the data
        $this->removeTestUserFromDB();

        // TODO :: Assert the response
        $response->assertJson(['success' => true]);
        $response->assertStatus(200);
    }

     public function testPasswordGetsHashed()
     {
         // TODO :: make sure the password is hashed
     }
}
