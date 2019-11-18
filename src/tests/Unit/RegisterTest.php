<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class RegisterTest extends TestCase
{
    private $validUsername = 'Mr Test Username';

    private $validEmail = 'mrtestemail@hotmail.com';

    private $validPassword = 'MrTestPa55word1';


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

    public function testGETRequest()
    {
        $response = $this->json('GET', '/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

    // public function testValidation ()
    // {
    //      // Test the validation of data
    // }

    public function testUpdateOfDatabase()
    {
        // Test addings user and table has that column
        $response = $this->submitValidUser();

        // Remove the data
        $this->removeTestUserFromDB();

        // Assert the response
        $response->assertJson([
            'success' => true
        ]);
    }

    private function submitValidUser (): ?Object
    {
        $data = [
            'username' => $this->validUsername,
            'email' => $this->validEmail,
            'password' => $this->validPassword
        ];
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ];
        
        // Send the request
        $response = $this->post('/register', $data, $headers);

        return $response;
    }

    // public function testPasswordGetsHashed()
    // {
    //     // make sure the password is hashed
    // }

    public function testRawPasswordIsRemoved()
    {
        // make sure the password is removed from everywhere
        // and create a user so we reach the block that removes the pass
        $response = $this->submitValidUser();
        // Remove the data
        $this->removeTestUserFromDB();
        $removed = $_POST['password'] ? false : true;
        $this->assertTrue($removed);
    }
}
