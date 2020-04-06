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
        TestUtilities::createTestUserInDb(['recover_token' => 'test_token']);
        $response = $this->get('/recover?token=test_token');
        $response->assertStatus(200);
        $response->assertViewIs('recover');
        $response->assertCookie('recoverToken');
        TestUtilities::removeTestUsersInDb();
    }

    public function testPost ()
    {
        // Test when getting user that doesnt exist
        $response = $this->sendPostRequest('', 'idontexist@hotmail.com', '');
        $response->assertJson(['success' => false, 'message' => 'Unable to authenticate']);
        $response->assertStatus(403);

        // Test it updates the row correctly
        TestUtilities::createTestUserInDb(['recover_token' => 'test_token']);
        $response = $this->sendPostRequest('test_token', TestUtilities::$validEmail, TestUtilities::$validPassword);
        $user = TestUtilities::getTestUserInDb();
        $this->assertEquals(true, $user->login_attempts === 3);
        $this->assertEquals(true, $user->recover_token === null);
        $this->assertEquals(true, Hash::check(TestUtilities::$validPassword, $user->password));

        // Assert json response
        $response->assertJson(['success' => true, 'message' => 'Successfully updated your password']);
    }
}
