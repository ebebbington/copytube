<?php

namespace Tests\Feature;

use App\BaseModel;
use App\User;
use App\UserModel;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    private $validUsername = 'ValidUsername';
    private $validEmail = 'testemail@hotmail.com';
    private $validPassword = 'TestPassword1';

    private function makePostRequest ($email, $password): ?Object
    {
        $data = [];
        if (isset($email)) $data['email'] = $email;
        if (isset($password)) $data['password'] = $password;

        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        // Send the request
        $response = $this->post('/login', $data, $headers);
        return $response;
    }

    private function createTestUser ($loginAttempts)
    {
        DB::table('users')->insert([
            'username' => $this->validUsername,
            'email_address' => $this->validEmail,
            'password' => UserModel::generateHash($this->validPassword),
            'login_attempts' => $loginAttempts,
            'logged_in' => 1
        ]);
    }

    private function removeTestUser ()
    {
        DB::table('users')->whereRaw("email_address = '$this->validEmail'")->delete();
    }

    public function testGetRequest()
    {
        $response = $this->json('GET', '/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function testPostLockedAccount ()
    {
        $this->createTestUser(0);
        // Send post request
        $response = $this->makePostRequest($this->validEmail, $this->validPassword);
        $response->assertJson(['success' => false, 'message' => 'This account has been locked.']);
        $response->assertStatus(403);
        $this->removeTestUser();
    }

    public function testPostIncorrectPasswordButValidEmail ()
    {
        $this->createTestUser(3);
        $response = $this->makePostRequest($this->validEmail, 'incorrectPassword');
        $response->assertJson(['success' => false, 'message' => 'Failed to authenticate']);
        $response->assertStatus(403);
        $this->removeTestUser();
    }

    // eg cannot auth attempt it as the user isnt in db
    public function testPostNonExistingUser ()
    {
        $response = $this->makePostRequest($this->validEmail, $this->validPassword);
        $response->assertJson(['success' => false, 'message' => 'This account does not exist with that email']);
        $response->assertStatus(403);
    }

    public function testPostSuccessfulLogin ()
    {
        $this->createTestUser(3);
        $response = $this->makePostRequest($this->validEmail, $this->validPassword);
        $response->assertJson(['success' => true]);
        $response->assertStatus(200);
        $this->removeTestUser();
    }
}
