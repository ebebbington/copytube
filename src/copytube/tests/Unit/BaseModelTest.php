<?php

namespace Tests\Unit;

use App\TestModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseModelTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function insertOne($value)
    {
        $Database = new DB();
        $Database::table("test")->insert(["test" => $value]);
    }

    private function deleteAllRows()
    {
        $Database = new DB();
        $Database::table("test")->delete();
    }

    public function testSelectQueryMethodWithCacheKey()
    {
        //
        // PASSING IN CACHE KEY
        //
        $Cache = new Cache();
        $Cache::flush();
        // Quickly create a row
        $value = "TEST1";
        $this->insertOne($value);
        $TestModel = new TestModel();
        $TestModel->CreateQuery(["test" => $value]);
        $cacheKey = "db:test:testkey";
        // Select the row passing in the key so its ready for next time
        $row = $TestModel->SelectQuery(
            ["where" => "test = '$value'", "limit" => 1],
            $cacheKey
        );
        // Select again and it should be the same
        $cachedRow = $TestModel->SelectQuery(
            ["where" => "test = '$value'", "limit" => 1],
            $cacheKey
        );
        $this->assertEquals($row, $cachedRow);
        $redisData = $Cache::get($cacheKey);
        $this->assertEquals($row, $redisData);
        $Cache::forget($cacheKey);
        //
        // Existing cache key - for code coverage
        //

        $Cache::put("test", "hi", 3600);
        $data = $TestModel->SelectQuery(["limit" => 1], "test");
        $this->assertEquals("hi", $data);
        $this->deleteAllRows();
    }

    public function testSelectQueryWithSelect()
    {
        // Selecting data
        $TestModel = new TestModel();
        $createdRow = $TestModel->CreateQuery(["test" => "Hello world"]);
        $selectedRow = $TestModel->SelectQuery([
            "select" => ["id", "test"],
            "limit" => 1,
        ]);
        $createdRowId = $createdRow->id;
        $selectedRowId = $selectedRow->id;
        $this->deleteAllRows();
        $this->assertEquals(true, isset($createdRowId));
        $this->assertEquals(true, isset($selectedRowId));
        $this->assertEquals($createdRowId, $selectedRowId);
    }

    public function testSelectQueryWithJoin()
    {
        // Joining
        $TestModel = new TestModel();
        $testFieldValue = "Something More";
        $TestModel->CreateQuery(["test" => $testFieldValue]);
        $selectedRow = $TestModel->SelectQuery([
            "select" => ["test.*", "videos.title"],
            "join" => ["videos", "test.test", "=", "videos.title"],
            "limit" => 1,
        ]);
        $this->deleteAllRows();
        $this->assertEquals($testFieldValue, $selectedRow->test);
        $this->assertEquals($testFieldValue, $selectedRow->title);
    }

    public function testSelectQueryWithWhere()
    {
        // Where
        $TestModel = new TestModel();
        $TestModel->CreateQuery(["test" => "Test2"]);
        $selectedRow = $TestModel->SelectQuery([
            "where" => "test = 'Test2'",
            "limit" => 1,
        ]);
        $this->deleteAllRows();
        $this->assertEquals("Test2", $selectedRow->test);
    }

    public function testSelectQueryWithLimit()
    {
        // Limit
        $TestModel = new TestModel();
        $TestModel->CreateQuery(["test" => "Test1"]);
        $TestModel->CreateQuery(["test" => "Test2"]);
        $selectedRow = $TestModel->SelectQuery(["limit" => 1]);
        $this->assertEquals(true, is_object($selectedRow));
        $selectedRows = $TestModel->SelectQuery(["limit" => 2]);
        $this->assertEquals(2, sizeof($selectedRows));
        $selectedRows = $TestModel->SelectQuery(["limit" => -1]);
        $this->assertEquals(true, is_object($selectedRows));
        $this->deleteAllRows();
    }

    public function testSelectQueryWithOrderBy()
    {
        // Order by
        $TestModel = new TestModel();
        $TestModel->CreateQuery(["test" => "3"]);
        $TestModel->CreateQuery(["test" => "1"]);
        $TestModel->CreateQuery(["test" => "2"]);
        $rows = $TestModel->SelectQuery([
            "limit" => 3,
            "orderBy" => ["column" => "test", "direction" => "DESC"],
        ]);
        $this->deleteAllRows();
        $this->assertTrue($rows[0]->test === "3");
        $this->assertTrue($rows[1]->test === "2");
        $this->assertTrue($rows[2]->test === "1");
    }

    public function testSelectQueryWhenEmpty()
    {
        // No data found expect false
        $this->deleteAllRows();
        $TestModel = new TestModel();
        $selectedRow = $TestModel->SelectQuery(["limit" => 1]);
        $this->assertEquals(false, $selectedRow);
    }

    public function testUpdateQueryMethodWithNoCacheKey()
    {
        // Expect correct result
        $TestModel = new TestModel();
        $testField = "Hello world";
        $TestModel->CreateQuery(["test" => $testField]);
        $success = $TestModel->UpdateQuery(
            ["test" => $testField],
            ["test" => "Goodbye world"]
        );
        $this->assertEquals(true, $success);
        $row = $TestModel->SelectQuery([
            "where" => "test = 'Goodbye world'",
            "limit" => 1,
        ]);
        $this->deleteAllRows();
        $this->assertEquals("Goodbye world", $row->test);
    }

    public function testUpdateQueryWithCacheKey()
    {
        // iF CACHE key passed in expect the key value pair to not exist anymore
        $Cache = new Cache();
        $cacheKey = "db:test:helloworld";
        $Cache::put($cacheKey, "hello", 3600);
        $TestModel = new TestModel();
        $testFieldValue = "Hello world 2";
        $TestModel->CreateQuery(["test" => $testFieldValue]);
        $TestModel->UpdateQuery(
            ["test" => $testFieldValue],
            ["test" => "Goodbye world 2"],
            $cacheKey
        );
        $redisData = $Cache::get($cacheKey);
        $expectedRedisData = new \stdClass();
        $expectedRedisData->id = 12;
        $expectedRedisData->test = "Goodbye world 2";
        $this->assertEquals($expectedRedisData, $redisData);
        $row = $TestModel->SelectQuery([
            "where" => "test = 'Goodbye world 2'",
            "limit" => 1,
        ]);
        $redisData = $Cache::get($cacheKey);
        $this->assertEquals($redisData, $row);
        $this->deleteAllRows();
    }

    public function testUpdateQueryOnFailure()
    {
        // Expect result to be false on error
        $TestModel = new TestModel();
        $success = $TestModel->UpdateQuery(
            ["test" => "I dont exist"],
            ["test" => "Hello world 99"]
        );
        $this->assertEquals(false, $success);
    }

    public function testDeleteQueryMethodOnSuccess()
    {
        // Expect redis to forget the cache key if passed in on success
        $Cache = new Cache();
        $cacheKey = "db:test:deleteQuery";
        $Cache::put($cacheKey, "hi", 3600);
        $TestModel = new TestModel();
        $testFieldValue = "Hello world 3";
        $TestModel->CreateQuery(["test" => $testFieldValue]);
        $success = $TestModel->DeleteQuery(
            ["test" => $testFieldValue],
            $cacheKey
        );
        $redisData = $Cache::get($cacheKey);
        $this->assertEquals(null, $redisData);
        $this->assertEquals(true, $success);
    }

    public function testDeleteQueryMethodOnFailure()
    {
        // Expect res to be false if failed
        $TestModel = new TestModel();
        $success = $TestModel->DeleteQuery(["test" => "I dont exist"]);
        $this->assertEquals(false, $success);
    }

    public function testCreateQueryMethod()
    {
        // Test it creates a row
        $this->deleteAllRows();
        $TestModel = new TestModel();
        $testFieldValue = "Hello world 4";
        $TestModel->CreateQuery(["test" => $testFieldValue]);
        $row = $TestModel->SelectQuery([
            "where" => "test = '$testFieldValue'",
            "limit" => 1,
        ]);
        $this->assertEquals($testFieldValue, $row->test);
        $this->deleteAllRows();

        // Test it forgets the cache key if passed in
        $Cache = new Cache();
        $cacheKey = "db:test:createQuery";
        $Cache::put($cacheKey, "hi", 3600);
        $TestModel->CreateQuery(["test" => "Hi"], $cacheKey);
        $redisData = $Cache::get($cacheKey);
        $this->assertEquals(null, $redisData);
        $this->deleteAllRows();
    }

    public function testNormalisingCacheKey()
    {
        // Expect any spaces to be replaced with "+"
        $exampleKey = "db:test:title=Something More";
        $replacedKey = "db:test:title=Something+More";
        $TestModel = new TestModel();
        $TestModel->CreateQuery(["test" => "Hi"]);
        $Cache = new Cache();
        $Cache::put($replacedKey, "Hi", 3600);
        $TestModel->UpdateQuery(
            ["test" => "Hi"],
            ["test" => "Bye"],
            $exampleKey
        );
        $redisData = $Cache::get($replacedKey);
        $this->assertEquals(true, (bool) $redisData);
    }

    public function testValidateMethod()
    {
        // Ensure it passes (true) if success and the error message when fails
        $TestModel = new TestModel();
        $res = $TestModel->validate(["test" => "hello"]);
        $this->assertEquals(true, $res);
        $res = $TestModel->validate([]);
        $this->assertEquals("The test field is required.", $res);
    }
}
