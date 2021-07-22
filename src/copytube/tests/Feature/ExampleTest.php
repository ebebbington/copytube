<?php

namespace Tests\Feature;

use App\Http\Controllers\RegisterController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpParser\Node\Expr\Cast\Object_;
use Tests\TestCase;

/**
 * Class ExampleTest
 *
 * @package Tests\Feature
 */
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get("/");

        $response->assertStatus(302);
    }

    /**
     * Another test example
     *
     * @test
     *
     * @return void
     */
    public function anotherBasicTest(): void
    {
        $this->assertTrue(true);
    }

    /**
     * A test can return a value which a dependant test can use. This is also called a producer
     */
    public function testReturnVal()
    {
        $arr = [1, 2, 3];
        $this->assertTrue($arr === [1, 2, 3]);
        return $arr;
    }

    /**
     * A test can depend on another test for a returned value. This is also called a consumer
     *
     * @param $val {Array}
     *
     * @depends testReturnVal
     *
     * @return void
     */
    public function testValIsArray(array $val)
    {
        $this->assertSame([1, 2, 3], $val);
    }

    /**
     * Create a setup functionality. The naming must be 'setUp' and this will be ran before each test
     */
    protected $stack;

    //    protected function setUp(): void
    //    {
    //        $this->stack = [];
    //    }

    /**
     * The same applies for for teaing down
     */
    //    protected function tearDown(): void
    //    {
    //
    //    }

    /**
     * You can also set up before a class
     */
    //    public static function setUpBeforeClass(): void
    //    {
    //
    //    }

    /**
     * And after
     */
    //    public static function tearDownAfterClass(): void
    //    {
    //
    //    }

    /**
     * Create a stub of a class
     * Note: this wont call the constructor of the class.
     *       this also needs use PHPUnit\Framework\TestCase; to access the createStub method
     *
     * @uses RegisterController::hello
     */
    //    public function testStub()
    //    {
    //        $stub = $this->createStub(RegisterController::class);
    //
    //        $stub->method('test')
    //            ->willReturn('hello'); // this will intentially fail
    //
    //        $this->assertSame('hello', $stub->test());
    //    }

    /**
     * You can also ignore classes or functions from code converage ussing the below
     *
     * @codeCoverageIgnore
     */
    public function youCantTestMeSoIgnoreMe()
    {
    }
}
