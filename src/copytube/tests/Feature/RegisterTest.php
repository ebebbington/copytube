<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
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

    // TODO :: testSubmitInvalidUser? Maybe this is handled by testvalidation

    private function makePostRequest ($username, $email, $password): ?Object
    {
        // TODO :: Add file object, how to mock file uploads?
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password
        ];
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
             $this->validPassword
         );
         $response->assertJson(['success' => false, 'message' => 'The username field is required.']);

         //
         // EMAIL
         //

         // TODO :: Empty

         // TODO :: Not an email

         //
         // PASSWORD
         //

         // TODO :: Empty

         // TODO :: No number

         // TODO :: Not correct length

         // TODO :: No letters

         // TODO :: No uppercase character

         // TODO :: No lowercase character
     }

     public function testPostWhenUserExists ()
     {
        // TODO
     }

     public function testProfilePictureIsSavedOnPost ()
     {
        // TODO
     }

    public function testSuccessfulPostRequest()
    {
        // First remove the test user if there is one
        $this->removeTestUserFromDB();

        // Test adding a user and that table has that column
        $response = $this->makePostRequest(
            $this->validUsername,
            $this->validEmail,
            $this->validPassword
        );

        // TODO :: Get user from DB and assert the data

        // Remove the data
        $this->removeTestUserFromDB();

        // TODO :: Assert the response
    }

     public function testPasswordGetsHashed()
     {
         // TODO :: make sure the password is hashed
     }
}
