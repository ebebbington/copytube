<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class RegisterTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSubmit()
    {
        // 
        $this->assertTrue(true);
    }

    public function testGETRequest()
    {
        $response = $this->json('GET', '/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

    public function testValidation ()
    {
         // Test the validation of data
    }

    public function testUpdateOfDatabase()
    {
        // Test addings user and table havs that column
        // Setup
        $data = [
            'username' => 'Edward Bebbington',
            'email' => 'Edkjbbington@hotmail.com',
            'password' => 'Welcome1'
        ];
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ];
        // Send the request
        $response = $this->post('/register', $data, $headers);

        // Assert the response
        $response->assertJson([
            'success' => true
        ]);

        // Remove the data
        DB::table('users')
            ->where('email_address', $data['email'])
            ->delete();
    }

    public function testPasswordGetsHashed()
    {
        // make sure the password is hashed
    }

    public function testRawPasswordIsRemoved()
    {
        // make sure the password is removed from everywhere

    }

    public function testPOSTRequest()
    {

        // Test it should be an ajax request and POST
    }
}
