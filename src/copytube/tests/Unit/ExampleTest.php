<?php

/**
 * Running tests
 *
 * Be inside the project root and not the tests directory.
 * Run vendor/bin/phpunit tests/Unit|Feature/Testfile.php
 * You can also add the 'testdox' flag to 'sectionise' the tests from the CLI
 *
 * All methods must be prefixed with "test"
 *
 * You can add code coverage by using the '--coverage-[html|php]' flag
 *
 * Be verbose in the cli
 *
 * You can always extend the test case class by creating an abstract subclass of it, and the test class inheriting that instead, e.g.
 * namespace PHPUnit\Framework
 * use PHP\Framework\TestCase
 * abstract class Assert {
 *     public function assertTrue(){}
 * }
 *
 * Feel free to customise the phpunit.xl file but keep a backup of the original
 *
 * Laravel 'mentions' that a unit test is really just focussing on small isolated portions, mainly a single method,
 * where as features are larger, how objects inetract with each other and full HTTP json endpoints
 *
 */

namespace Tests\Unit;

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
