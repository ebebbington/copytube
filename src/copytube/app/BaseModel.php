<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\Boolean;

class BaseModel extends Model
{

    private function normaliseCacheKey($cacheKey = '')
    {
        $replacedKey = str_replace(' ', '+', $cacheKey);
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        Log::info($loggingPrefix . "Replaced $cacheKey with $replacedKey");
        return $replacedKey;
    }

    /**
     * Validate Data for a mode, such as before inserting.
     *
     * This method will check the passed in data with the $rules property of the INHERITED class
     * e.g. the calling class must extend this one
     *
     * @param array $data containing an array of key value pairs
     *
     * @return bool|string true if a success, else the error message as it must have failed
     * @example
     * $UserModel = new UserModel;
     * $validated = $UserModel->validate(['name' => 'edward']);
     *
     */
    public function validate(array $data)
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        Log::info($loggingPrefix . 'Going to validate the following data: ', $data);
        Log::info($loggingPrefix . 'With the rules of: ', $this->rules);
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            Log::error($loggingPrefix . 'Validation failed');
            return $validator->errors()->first();;
        }
        Log::info($loggingPrefix . 'Validation passed');
        return true;
    }

    /**
     * @param bool|object $Model
     */
    private function populate($Model = false)
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        if ($Model !== false && !empty($Model)) {
            Log::info($loggingPrefix . 'Passed in Model is not empty [GOOD]: ', [$Model]);
            foreach ($Model as $key => $value) {
                if (property_exists($this, $key)) {
                    Log::info($loggingPrefix . 'Calling class has property of ' . $key . ". Setting it to $value");
                    $this->$key = $value;
                    //$this[$key] = $value;
                }
            }
        }
    }

    /**
     * @method SelectQuery
     *
     * @description
     * Can handle any GET/SELECT database queries
     *
     * @param array  $query    Contains the required data to run the query you want.                    Required.
     *                         $data = [
     *                         'select'           =>  (string|string[]) List or string of fields to select. Omitted if not defined e.g it'll end up selecting all. Optional
     *                         'join'             =>  (array) Array of strings on what to join. Optional
     *                         'query'            =>  (string) Where condition. Defaults to having none (id != -1). Optional
     *                         'limit'            =>  (int) If 1 returns an object. If -1 gets all. If > 1 gets many.   Required.
     *                         'orderByColumn'    =>  (string) Must be used with `orderByDirection`. Defaults to `id`
     *                         'orderByDirection' =>  (string) Must be used with above. Defaults to 'ASC'
     *                         ]
     * @param string $cacheKey Gets db data by key else creates the key data.                      Optional.
     *
     * @return bool|array|object False when no data found, singular object if limit=1 result, array of objects when more than 1
     *
     * @example
     * $select = ['comments.*, users.profile_picture']
     * $join = ['users', 'comments.user_id', '=', 'users.id']
     *          ^^^^^      ^^^^^^^^^^^       ^^    ^^^^^^
     *     other table       where      conditional where
     * $where = "name = '$name' and age != 200"; // or omit this property
     * $limit = 1; // -1 = all (array), 1 = 1 (object), >1 = many (array)
     * $orderBy = [
     *   'column' => date, // defaults to id
     *   'direction' => 'ASC' //defaults to ASC. Supported: ASC, DESC
     * ];
     * $query = ['select' => $select, 'join' => $join, 'where' => $where, 'limit' => $limit, 'orderBy' => $orderBy];
     * $cacheKey = 'db:users:name=edward&age!=200&limit=1';
     * $SomeModel->SelectQuery($query, $cacheKey);
     */
    public function SelectQuery(array $query, string $cacheKey = '')
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        $cacheKey = $this->normaliseCacheKey($cacheKey);
        // If the cached data already exists with the given key then return that instead
        Log::info($loggingPrefix . 'Passed in `cacheKey` is: ' . $cacheKey);
        if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
            Log::info($loggingPrefix . 'Redis has the cached data for that key. Returning this instead');
            return Cache::get($cacheKey);
        }
        $select = isset($query['select']) ? $query['select'] : null;
        $join = isset($query['join']) ? $query['join'] : null;
        $where = isset($query['where']) ? $query['where'] : null;
        $limit = $query['limit'] ?? 1;
        $orderByColumn = $query['orderBy']['column'] ?? 'id';
        $orderByDirection = $query['orderBy']['direction'] ?? 'ASC';
        Log::info($loggingPrefix . 'Running query on table' . $this->table . ' where ' . $where . ', with a limit of ' . $query['limit']
            . '. Ordering ' . $orderByColumn . ' by ' . $orderByDirection);

        $result = DB::table($this->table);

        if (isset($select))
            $result = $result->select($select);

        if (isset($join) && sizeof($join) === 4)
            $result = $result->join($join[0], $join[1], $join[2], $join[3]);

        if (isset($where))
            $result = $result->whereRaw($where);

        $result = $result->orderBy($orderByColumn, $orderByDirection);

        if (isset($limit))
            $result = $result->take($limit);

        $result = $result->get();

        if ($result->toArray() === [])
            return false;
        if (empty($result))
            return false;
        if (!isset($result))
            return false;
        if (!$result)
            return false;

        // When asking for 1 record, return a single object as they dont expect an array
        if ($limit === 1 && sizeof($result) >= 1)
            $result = $result[0];

        // Cache the result
        if ($cacheKey && !empty($cacheKey) && isset($cacheKey))
            Cache::put($cacheKey, $result, 3600);

        return $result;
    }

    /**
     * @method CreateQuery
     *
     * @description
     * Add data to a database. Will also flush the cache key if it exists so on a similar select,
     * will save the new data
     *
     * @param array  $data     Key value pairs of data to insert
     * @param string $cacheKey The key of the cache to update, only applies if updating an array
     *
     * @return mixed The database row just inserted
     * @example
     * $UserModel = new UserModel;
     * $User = $UserModel->CreateQuery(['name' => 'edward', ...])
     *
     */
    public function CreateQuery(array $data, string $cacheKey = '')
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        $cacheKey = $this->normaliseCacheKey($cacheKey);
        Log::info($loggingPrefix . 'Creating a new row on table ' . $this->table . ':', $data);
        $row = $this->create($data);
        if (!empty($cacheKey) && Cache::has($cacheKey)) {
            // Because next time they select with the key, we dont want them selecting old data
            // So on the select it'll make a new key with the updated data
            Log::info($loggingPrefix . 'Redis cache has the passed in key of ' . $cacheKey . '. Forgetting this data to update it on the next select');
            Cache::forget($cacheKey);
        }
        Log::info($loggingPrefix . 'Returning the newly created row');
        return $row;
    }

    /**
     * Update a model BY data WITH new data
     *
     * Run a query using $query to find the data, then update it using $newData
     *
     * @param array  $query    The key value pair of data to find
     * @param array  $newData  The key value pair of data to update
     * @param string $cacheKey The key associated with the data to update
     *
     * @return bool true or false based on the success
     * @example
     * $UserModel = new UserModel;
     * $updated = $UserModel->UpdateQuery([...], [...]); // true or false
     *
     */
    public function UpdateQuery(array $query, array $newData, string $cacheKey = '')
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        $cacheKey = $this->normaliseCacheKey($cacheKey);
        Log::info($loggingPrefix . 'Updating ' . $this->table . 'with the query and new data:', [$query, $newData]);
        $result = DB::table($this->table)->where($query)->update($newData);
        if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
            Log::info($loggingPrefix . 'Redis cache has key of ' . $cacheKey . '. Updating the cache with the result:', [$result]);
            $row = DB::table($this->table)->where($newData)->first();
            Log::debug(json_encode($row));
            Cache::put($cacheKey, $row, 3600);
        }
        Log::debug($loggingPrefix . 'Query has a successful update: ' . $result === 1 ? true : false);
        return $result >= 1 ? true : false;
    }

    /**
     * @param array  $query
     * @param string $cacheKey The key associated with the data to delete
     *
     * @return int
     */
    public function DeleteQuery(array $query, string $cacheKey = '')
    {
        $loggingPrefix = "[BaseModel - ".__FUNCTION__.'] ';
        $cacheKey = $this->normaliseCacheKey($cacheKey);
        if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
            Log::info($loggingPrefix . 'Cache has key of ' . $cacheKey . '. Removing this key');
            Cache::forget($cacheKey);
        }
        $result = DB::table($this->table)->where($query)->delete();
        $success = $result === 1 || $result === true ? true : false;
        Log::info($loggingPrefix . 'Deletion on table ' . $this->table . ' with the following has a success of: ' . $success, $query);
        return $success;
    }
}
