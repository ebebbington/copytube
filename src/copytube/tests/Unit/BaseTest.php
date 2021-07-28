<?php

namespace Tests\Unit;

use App\Base;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testValidateMethod()
    {
        // Ensure it passes (true) if success and the error message when fails
        $res = Base::validate(["test" => "hello"], ["test" => "required"]);
        $this->assertEquals(true, $res);
        $res = Base::validate([], ["test" => "required"]);
        $this->assertEquals("The test field is required.", $res);
    }
}
