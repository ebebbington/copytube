<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatTest extends TestCase
{
    public function testGetRequest()
    {
        $response = $this->json('GET', '/chat');
        $response->assertStatus(200);
        $response->assertViewIs('chat');
    }
}
