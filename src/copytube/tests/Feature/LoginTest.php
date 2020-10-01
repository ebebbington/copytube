<?php

namespace Tests\Feature;

use App\BaseModel;
use App\User;
use App\UserModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

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

    public function testGetRequestWhenNotAuthed()
    {
        $response = $this->json('GET', '/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function testPostLockedAccount ()
    {
        Cache::flush();
        TestUtilities::removeTestUsersInDb();
        TestUtilities::createTestUserInDb(['login_attempts' => 0]);
        // Send post request
        $response = $this->makePostRequest(TestUtilities::$validEmail, TestUtilities::$validPassword);
        $response->assertJson(['success' => false, 'message' => 'This account has been locked.']);
        $response->assertStatus(403);
        TestUtilities::removeTestUsersInDb();
    }

    public function testPostIncorrectPasswordButValidEmail ()
    {
        TestUtilities::createTestUserInDb();
        $response = $this->makePostRequest(TestUtilities::$validEmail, TestUtilities::$invalidPasswords[0]);
        $response->assertJson(['success' => false, 'message' => 'Failed to authenticate']);
        $response->assertStatus(403);
        TestUtilities::removeTestUsersInDb();
    }

    // eg cannot auth attempt it as the user isnt in db
    public function testPostNonExistingUser ()
    {
        $response = $this->makePostRequest(TestUtilities::$validEmail, TestUtilities::$validPassword);
        $response->assertJson(['success' => false, 'message' => 'This account does not exist with that email']);
        $response->assertStatus(403);
    }

    public function testPostSuccessfulLogin ()
    {
        TestUtilities::createTestUserInDb();
        $response = $this->makePostRequest(TestUtilities::$validEmail, TestUtilities::$validPassword);
        $user = DB::table('users')->where('email_address', '=', TestUtilities::$validEmail)->first();
        $this->assertEquals(0, $user->logged_in);
        $response->assertJson(['success' => true]);
        $response->assertStatus(200);
        TestUtilities::removeTestUsersInDb();
    }
}
