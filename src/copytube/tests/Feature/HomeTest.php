<?php

namespace Tests\Feature;

use App\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testGetWithAuth()
    {
        $id = TestUtilities::createTestUserInDb();
        TestUtilities::logUserIn($id);
        $response = $this->json("GET", "/home");
        $response->assertStatus(200);
        $response->assertViewIs("home");
        TestUtilities::removeTestUsersInDb();
    }

    public function testGetWithoutAuth()
    {
        TestUtilities::removeTestUsersInDb();
        $response = $this->json("GET", "/home");
        //$response->assertStatus(401);
        $response->assertSee("Unauthenticated");
    }
}
