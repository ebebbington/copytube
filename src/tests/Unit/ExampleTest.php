<?php

/**
 * Running tests
 * 
 * Be inside the project root and not the tests directory.
 * Run vendor/bin/phpunit tests/Unit|Feature/Testfile.php
 * 
 * Writing a testable class
 * 
 * All methods must be prefixed with "test"
 * 
 */

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }
}
