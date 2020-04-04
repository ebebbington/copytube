<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecoverTest extends TestCase
{
    private function sendPostRequest ($token, $email, $password)
    {
        $data = [
            'recoverToken' => $token,
            'email' => $email,
            'password' => $password
        ];
        $headers = [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token()
        ];
        // Send the request
        $response = $this->post('/recover', $data, $headers);
        return $response;
    }

    private function createTestUser ()
    {
        DB::table('users')->insert([
            'username' => 'Test username',
            'email_address' => 'Testemail@hotmail.com',
            'password' => UserModel::generateHash('Testpassword1'),
            'logged_in' => 0,
            'login_attempts' => 0,
            'recover_token' => 'test_token',
        ]);
    }

    private function deleteTestUser ()
    {
        DB::table('users')->where('email_address', '=', 'Testemail@hotmail.com')->delete();
    }

    public function testGetWithIncorrectToken ()
    {
        // No query
        $response = $this->get('/recover');
        $response->assertStatus(302);
        $response->assertRedirect('login');

        // No value
        $response = $this->get('/recover?token=');
        $response->assertStatus(302);
        $response->assertRedirect('login');

        // Incorrect value
        $response = $this->get('/recover?token=Idontexist');
        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testGetWithCorrectToken ()
    {
        // Assert correct response with correct token
        $this->createTestUser();
        $response = $this->get('/recover?token=test_token');
        $response->assertStatus(200);
        $response->assertViewIs('recover');
        $response->assertCookie('recoverToken');
        $this->deleteTestUser();
    }

    public function testPost ()
    {
        // Test when getting user that doesnt exist
        $response = $this->sendPostRequest('', 'idontexist@hotmail.com', '');
        $response->assertJson(['success' => false, 'message' => 'Unable to authenticate']);
        $response->assertStatus(403);

        // Test it updates the row correctly
        $this->createTestUser();
        $response = $this->sendPostRequest('test_token', 'Testemail@hotmail.com', 'Testpassword2');
        $user = DB::table('users')->where('email_address', '=', 'Testemail@hotmail.com')->first();
        $this->assertEquals(true, $user->login_attempts === 3);
        $this->assertEquals(true, $user->recover_token === null);
        $this->assertEquals(true, Hash::check('Testpassword2', $user->password));

        // Assert json response
        $response->assertJson(['success' => true, 'message' => 'Successfully updated your password']);
    }
}
