<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testGenerateHashMethod()
    {
        $rawPass = "Hello";
        $hash = User::generateHash($rawPass);
        $this->assertEquals(true, Hash::check($rawPass, $hash));
    }
}
