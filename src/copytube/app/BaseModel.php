<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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
     * @method SelectQuery
     *
     * @description
     * Can handle any GET/SELECT database queries
     *
     * @param array $query Contains the required data to run the query you want.                    Required.
     *   $data = [
     *     'query'            =>  (string) Where condition. Defaults to having none (id != -1)
     *     'limit'            =>  (int) If 1 returns an object. If -1 gets all. If > 1 gets many.   Required.
     *     'orderByColumn'    =>  (string) Must be used with `orderByDirection`. Defaults to `id`
     *     'orderByDirection' =>  (string) Must be used with above. Defaults to 'ASC'
     *   ]
     * @param string $cacheKey  Gets db data by key else creates the key data.                      Optional.
     *
     * @return bool|array|object False when no data found, singular object if one result, array of objects when more than 1
     *
     * @example
     * $where = "name = '$name' and age != 200"; // or omit this property
     * $limit = 1; // -1 = all (array), 1 = 1 (object), >1 = many (array)
     * $orderBy = [
     *   'column' => date, // defaults to id
     *   'direction' => 'ASC' //defaults to ASC. Supported: ASC, DESC
     * ];
     * $query = ['where' => $where, 'limit' => $limit, 'orderBy' => $orderBy];
     * $cacheKey = 'db:users:name=edward&age!=200&limit=1';
     * $SomeModel->SelectQuery($query, $cacheKey);
     */
    public function SelectQuery (array $query, string $cacheKey = '')
    {
      $where = $query['where'] ?? 'id != -1';
      $limit = $query['limit'];
      $orderByColumn = $query['orderBy']['column'] ?? 'id';
      $orderByDirection = $query['orderBy']['direction'] ?? 'ASC';
      // If the cached data already exists with the given key then return that instead
      if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
          Log::debug('cache has key of ' . $cacheKey . '. Returning this data instead');
          return Cache::get($cacheKey);
      }
        Log::debug('Running a SELECT query where ' . $query['where'] . ', with a limit of ' . $query['limit'] . '. Ordering ' .$orderByColumn . ' by ' . $orderByDirection);
        $result = DB::table($this->table)->whereRaw($where)->orderBy($orderByColumn, $orderByDirection)->take($limit)->get();
      // When asking for 1 record, return a single object as they dont expect an array
      if ($limit === 1 && !empty($result))
          $result = $result[0];
      if (empty($result) || !isset($result))
          return false;
      return $result;
    }

    /**
     * Add data to a database
     *
     * @example
     * $UserModel = new UserModel;
     * $User = $UserModel->CreateQuery(['name' => 'edward', ...])
     *
     * @param array $data Key value pairs of data to insert
     * @param string $cacheKey The key of the cache to update, only applies if updating an array
     *
     * @return mixed The database row just inserted
     */
    public function CreateQuery (array $data, string $cacheKey = '')
    {
        Log::debug('Going to run a create query using: ');
        Log::debug(json_encode($data));
        $row = $this->create($data);
        if (!empty($cacheKey) && $cacheData = Cache::has($cacheKey)) {
            // Push a new item to the array
            if (is_array($cacheData)) {
                array_unshift($cacheData, $row);
                Cache::put($cacheKey, $cacheData, 3600);
            }
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
     * @param string $cacheKey The key associated with the data to update
     * @return bool true or false based on the success
     */
    public function UpdateQuery (array $query, array $newData, string $cacheKey = '')
    {
      $result = DB::table($this->table)->where($query)->update($newData);
      if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
          $row = DB::table($this->table)->where($query)->first();
          Cache::put($cacheKey, $row, 3600);
      }
      return $result === 1 ? true : false;
    }

    /**
     * @param array $query
     * @param string $cacheKey The key associated with the data to delete
     *
     * @return int
     */
    public function DeleteQuery (array $query, string $cacheKey = '')
    {
        if ($cacheKey && !empty($cacheKey) && Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        return DB::table($this->table)->where($query)->delete();
    }
}
