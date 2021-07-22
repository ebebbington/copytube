<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testGetWithAuth()
    {
        $userId = TestUtilities::createTestUserInDb();
        TestUtilities::logUserIn($userId);
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
