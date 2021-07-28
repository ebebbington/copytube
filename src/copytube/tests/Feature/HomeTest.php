<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testGetWithAuth()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user["id"]);
        $response = $this->json("GET", "/home");
        $response->assertStatus(200);
        $response->assertViewIs("home");
    }

    public function testGetWithoutAuth()
    {
        $response = $this->json("GET", "/home");
        //$response->assertStatus(401);
        $response->assertSee("Unauthenticated");
    }
}
