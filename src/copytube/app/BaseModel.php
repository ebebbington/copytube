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
     * $validated = $User->validate(['name' => 'edward']);
     *
     * @param Array $data containing an array of key value pairs
     *
     * @return Boolean
     */
    public function validate(array $data)
    {
        $test = $this->rules;
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
          return false;
        }
        return true;
    }
}