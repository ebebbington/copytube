<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
     * @param Array $data containing an array of key value pairs
     *
     * @return Boolean
     */
    public function validate(array $data)
    {
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
          return false;
        }
        return true;
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
     * // No data will match this query
     * $result = $SomeModel->Select(['name' => 'i dont exist']); // false
     * // Get single data
     * $result = $SomeModel->Select([...], true); // defaults to true
     * $id = $result->id;
     * // Get all data
     * $result = $SomeModel->Select([...], false) // [[[...]], [{...}]]
     *
     * @param array $data Key value pair array of data to use in the wher clause
     * @param boolean Find a single instance? Defaults to true
     *
     * @return boolean|array False when no data found, singular object if one result, array of objects when more than 1
     */
    public function SelectQuery (array $data, bool $first = true)
    {
      // Allow to get only a single result for dynamics, and make this a priority to limit querying
      $result = null;
      if ($first) {
        $result = DB::table($this->table)->where($data)->first();
      }
      // then check if we arent looking for a single row
      if ($first === false) {
        $result = DB::table($this->table)->where($data)->get();
      }
      // No data found matching query?
      if (empty($result)) {
        return false;
      }
      // Dev should know when calling it how the data is returned e.g. if first = true, a single object is returned
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
     *
     * @return mixed
     */
    public function CreateQuery (array $data)
    {
        Log::debug('Going to run a create query using: ');
        Log::debug(print_r($data));
        $model = $this->create($data);
        return $model;
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
     * @return boolean true or false based on the success
     */
    public function UpdateQuery (array $query, array $newData): bool
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
        $result = DB::table($this->table)->where($query)->delete();
        return $result;
    }
}
