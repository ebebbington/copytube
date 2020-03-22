<?php

namespace App;

use App\Helpers\RedisCacheHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Boolean;

class BaseModel extends Model
{
    /**
     * Validate Data for a mode, such as before inserting.
     *
     * This method will check the passed in data with the $rules property of the INHERITED class
     * e.g. the calling class must extend this one
     *
     * @example
     * $UserModel = new UserModel;
     * $validated = $UserModel->validate(['name' => 'edward']);
     *
     * @param array $data containing an array of key value pairs
     *
     * @return bool
     */
    public function validate(array $data)
    {
      Log::debug(json_encode($data));
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
          return false;
        }
        return true;
    }

    private function populate ($Model = false)
    {
      $a = $Model;
      $b='';
      if ($Model !== false && !empty($Model)) {
        foreach ($Model as $key => $value) {
          if (property_exists($this, $key)) {
            $this->$key = $value;
            //$this[$key] = $value;
          }
        }
      }
    }

    /**
     * SELECT queries
     *
     * Will get all results based on the where clauses in the params and
     * the calling classes table name
     *
     * Should the result only be one, it will return a single array that can
     * be used like such: $result->id
     *
     * Should t retrieve multiple rows, it will return that array
     * e.g. $result[0]->name; $result[3]->name;
     *
     * @example
     * // Get a single record
     * $data = [
     *  'query' => ['name' => 'Edward'],
     *  'selectOne' => true,
     * ]
     * $result = $SomeModel->Select($data); // {...} or false
     * // Many records
     * $data = [
     *  'query' => ['username' => 'edward'],
     *  'selectOne' => false,
     *  'count' => 102
     * ]
     * $result = $SomeModel->Select($data);
     * // Get all data
     * $data = [
     *  'query' => [],
     *  'selectOne' => false,
     *  'count' => null
     * ]
     * $result = $SomeModel->Select($data) // [[[...]], [{...}]]
     * // Use conditionals
     * $data = [
     *  'query' => 'name',
     *  'conditionalOperator' => '!=',
     *  'conditionalValue' => 'edward',
     *  'count' => 102,
     *  'selectOne' => false
     * ]
     * $result = $SomeModel->Select('title', false, 0, '!=', 'Something More')
     *
     * @param array|string $data Can be a key value pair for data to find, or a string if used with conditionals, e.g. ('title', $first = false, $limit = 0, $operator = '!=', $value = 'username' )
     * @param boolean Find a single instance? Defaults to true
     *
     * @return bool|array|object False when no data found, singular object if one result, array of objects when more than 1
     */
    public function SelectQuery (array $data = [], bool $first = true, int $limit = 0)
    {
      $query = $data['query'] ?? [];
      $conditionalOperator = $data['conditionalOperator'] ?? '';
      $conditionalValue = $data['conditionalValue'] ?? '';
      $count = $data['count'] ?? null;
      $selectOne = $data['selectOne'] ?? true;
      $orderByColumn = $data['orderBy']['column'] ?? 'id';
      $orderByDirection = $data['orderBy']['direction'] ?? 'ASC';
      $cacheKey = $data['cacheKey'] ?? null;
      if ($cacheData = RedisCacheHelper::get($cacheKey)) {
          return $cacheData;
      }
      $passedInData = [
        'query' => [
          'name' => 'edward'
        ],
        'conditionalOperator' => '!=',
        'conditionalValue' => 'hello',
        'count' => 5,
        'selectOne' => true,
        'orderBy' => ['column' => 'id', 'direction' => 'ASC'],
        'cacheKey' => 'some key' // e.g. on home controller for getting dynamic video: cacheKey = 'videos:something_more' or 'videos:something_more,iceland'
      ];
      // Get a single record if requested
      if ($selectOne) {
        $result = DB::table($this->table)->where($query, $conditionalOperator, $conditionalValue)->first();
        if (empty($result))
          return false;
        // When we get data save the data is it doesn't exist to redis, populate class and return
          if ($cacheKey && !$cacheData = RedisCacheHelper::get($cacheKey)) {
              RedisCacheHelper::set($result, $cacheKey);
          }
          $this->populate($result);
          return $result;
      }
      // Get all by the count limiter
      if ($selectOne === false && $count > 1) {
        $result = DB::table($this->table)->where($query, $conditionalOperator, $conditionalValue)->orderBy($orderByColumn, $orderByDirection)->take($count)->get();
        if (empty($result))
            return false;
        if ($cacheKey && !$cacheData = RedisCacheHelper::get($cacheKey)) {
              RedisCacheHelper::set($result, $cacheKey);
          }
        return $result; // [{...}, {...}]
      }
      // Get all if count is undefined
      if ($selectOne === false && empty($count)) {
        $result = DB::table($this->table)->where($query, $conditionalOperator, $conditionalValue)->orderBy($orderByColumn, $orderByDirection)->get();
        if (empty($result))
            return false;
          if ($cacheKey && !$cacheData = RedisCacheHelper::get($cacheKey)) {
              RedisCacheHelper::set($result, $cacheKey);
          }
        return $result; // [{...}, {...}]
      }
    }

    /**
     * Add data to a database
     *
     * @example
     * $UserModel = new UserModel;
     * $User = $UserModel->CreateQuery(['name' => 'edward', ...])
     *
     * @param array $data Key value pairs of data to insert
     * @param string $cacheKey The key of the cache to update
     *
     * @return mixed The database row just inserted
     */
    public function CreateQuery (array $data, string $cacheKey)
    {
        Log::debug('Going to run a create query using: ');
        Log::debug(json_encode($data));
        $row = $this->create($data);
        if ($cacheKey && $cacheData =  RedisCacheHelper::get($cacheKey)) {
            RedisCacheHelper::update($data, $cacheKey);
        }
        return $row;
    }

    /**
     * Update a model BY data WITH new data
     *
     * Run a query using $query to find the data, then update it using $newData
     *
     * @example
     * $UserModel = new UserModel;
     * $updated = $UserModel->UpdateQuery([...], [...]); // true or false
     *
     * @param array $query The key value pair of data to find
     * @param array $newData The key value pair of data to update
     * @return bool true or false based on the success
     */
    public function UpdateQuery (array $query, array $newData)
    {
      $result = DB::table($this->table)->where($query)->update($newData);
      return $result === 1 ? true : false;
    }

    /**
     * @param array $query
     *
     * @return int
     */
    public function DeleteQuery (array $query)
    {
        return DB::table($this->table)->where($query)->delete();
    }
}
