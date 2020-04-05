<?php

namespace Tests\Unit;

use App\TestModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use \Illuminate\Container\Container as Container;
use \Illuminate\Support\Facades\Facade as Facade;

class BaseModelTest extends TestCase
{
    private function insertOne ($value)
    {
        DB::table('test')->insert(['test' => $value]);
    }

    private function deleteAllRows ()
    {
        DB::table('test')->delete();
    }

    public function testSelectQueryMethod ()
    {
        //
        // PASSING IN CACHE KEY
        //

        // Quickly create a row
        $value = 'TEST1';
        $this->insertOne($value);
        $TestModel = new TestModel;
        $TestModel->CreateQuery(['test' => $value]);
        // Select the row passing in the key so its ready for next time
        $row = $TestModel->SelectQuery(['where' => "test = '$value'", 'limit' => 1], 'db:test:testkey');
        // Select again and it should be the same
        $cachedRow = $TestModel->SelectQuery(['where' => "test = '$value'", 'limit' => 1], 'db:test"testkey');
        $this->assertEquals($row, $cachedRow);
        $redisData = Cache::get('db:test:testkey');
        $this->assertEquals($row, $redisData);
        Cache::forget('db:test:testkey');
        $this->deleteAllRows();

        //
        // USING SELECT PROPERTY
        //

        // Selecting data
        $createdRow = $TestModel->CreateQuery(['test' => 'Hello world']);
        $selectedRow = $TestModel->SelectQuery(['select' => ['id', 'test'], 'limit' => 1]);
        $createdRowId = $createdRow->id;
        $selectedRowId = $selectedRow->id;
        $this->assertEquals(true, isset($createdRowId));
        $this->assertEquals(true, isset($selectedRowId));
        $this->assertEquals($createdRowId, $selectedRowId);
        $this->deleteAllRows();

        //
        // JOIN PROPERTY
        //

        // Joining
        $TestModel->CreateQuery(['test' => 'Something More']);
        $selectedRow = $TestModel->SelectQuery([
            'select' => ['test.*', 'videos.title'],
            'join' => ['videos', 'test.test', '=', 'videos.title'],
            'limit' => 1
        ]);
        $this->assertEquals('Something More', $selectedRow->test);
        $this->assertEquals('Something More', $selectedRow->title);
        $this->deleteAllRows();

        //
        // WHERE
        //

        // Where
        $TestModel->CreateQuery(['test' => 'Test2']);
        $selectedRow = $TestModel->SelectQuery([
            'where' => "test = 'Test2'",
            'limit' => 1
        ]);
        $this->assertEquals('Test2', $selectedRow->test);
        $this->deleteAllRows();

        //
        // LIMIT
        //

        // Limit
        $TestModel->CreateQuery(['test' => 'Test1']);
        $TestModel->CreateQuery(['test' => 'Test2']);
        $selectedRow = $TestModel->SelectQuery(['limit' => 1]);
        $this->assertEquals(true, is_object($selectedRow));
        $selectedRows = $TestModel->SelectQuery(['limit' => 2]);
        $this->assertEquals(2, sizeof($selectedRows));
        $selectedRows = $TestModel->SelectQuery(['limit' => -1]);
        $this->assertEquals(true, is_object($selectedRows));
        $this->deleteAllRows();

        //
        // ORDER BY
        //

        // TODO :: Order by

        //
        // EMPTY
        //

        // No data found expect false
        $this->deleteAllRows();
        $selectedRow = $TestModel->SelectQuery(['limit' => 1]);
        $this->assertEquals(false, $selectedRow);

        //
        // Existing cache key - for code coverage
        //

        Cache::put('test', 'hi', 3600);
        $data = $TestModel->SelectQuery(['limit' => 1], 'test');
        $this->assertEquals('hi', $data);

    }

    public function testUpdateQueryMethod ()
    {
        // Expect correct result
        $TestModel = new TestModel;
        $TestModel->CreateQuery(['test' => 'Hello world']);
        $success = $TestModel->UpdateQuery(['test' => 'Hello world'], ['test' => 'Goodbye world']);
        $this->assertEquals(true, $success);
        $row = $TestModel->SelectQuery(['where' => "test = 'Goodbye world'", 'limit' => 1]);
        $this->assertEquals('Goodbye world', $row->test);
        $this->deleteAllRows();

        // iF CACHE key passed in expect the key value pair to not exist anymore
        Cache::put('db:test:helloworld', 'hello', 3600);
        $TestModel->CreateQuery(['test' => 'Hello world']);
        $TestModel->UpdateQuery(['test' => 'Hello world'], ['test' => 'Goodbye world'], 'db:test:helloworld');
        $redisData = Cache::get('db:test:helloworld');
        $row = $TestModel->SelectQuery(['where' => "test = 'Goodbye world'", 'limit' => 1]);
        $redisData = Cache::get('db:test:helloworld');
        $this->assertEquals($redisData, $row);
        $this->deleteAllRows();

        // Expect result to be false on error
        $success = $TestModel->UpdateQuery(['test' => 'I dont exist'], ['test' => 'Hello world']);
        $this->assertEquals(false, $success);
    }

    public function testDeleteQueryMethod ()
    {
        // Expect redis to forget the cache key if passed in on success
        Cache::put('db:test:deleteQuery', 'hi', 3600);
        $TestModel = new TestModel;
        $TestModel->CreateQuery(['test' => 'Hello world']);
        $success = $TestModel->DeleteQuery(['test' => 'Hello world'], 'db:test:deleteQuery');
        $redisData = Cache::get('db:test:deleteQuery');
        $this->assertEquals(null, $redisData);
        $this->assertEquals(true, $success);

        // Expect res to be false if failed
        $success = $TestModel->DeleteQuery(['test' => 'I dont exist']);
        $this->assertEquals(false, $success);
    }

    public function testCreateQueryMethod ()
    {
       // Test it creates a row
        $this->deleteAllRows();
        $TestModel = new TestModel;
        $TestModel->CreateQuery(['test' => 'Hello World']);
        $row = $TestModel->SelectQuery(['where' => "test = 'Hello World'", 'limit' => 1]);
        $this->assertEquals('Hello World', $row->test);
        $this->deleteAllRows();

        // Test it forgets the cache key if passed in
        Cache::put('db:test:createQuery', 'hi', 3600);
        $TestModel->CreateQuery(['test' => 'Hi'], 'db:test:createQuery');
        $redisData = Cache::get('db:test:createQuery');
        $this->assertEquals(null, $redisData);
        $this->deleteAllRows();
    }

    public function testNormalisingCacheKey ()
    {
        // TODO :: Expect any spaces to be replaced with "+"
        $exampleKey = "db:test:title=Something More";
        $replacedKey = "db:test:title=Something More";
        $TestModel = new TestModel;
        $TestModel->CreateQuery(['test' => 'Hi']);
        Cache::put($replacedKey, 'Hi', 3600);
        $TestModel->UpdateQuery(['test' => 'Hi'], ['test' => 'Bye'], $exampleKey);
        $redisData = Cache::get($replacedKey);
        $this->assertEquals(true, !!$redisData);
    }

    public function testValidateMethod ()
    {
        // TODO :: Ensure it passes (true) if success and the error message when fails
        $TestModel = new TestModel;
        $res = $TestModel->validate(['test' => 'hello']);
        $this->assertEquals(true, $res);
        $res = $TestModel->validate([]);
        $this->assertEquals('The test field is required.', $res);
    }
}
