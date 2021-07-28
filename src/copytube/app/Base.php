<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Base extends Model
{
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
     * $User = new User;
     * $validated = $User->validate(['name' => 'edward']);
     *
     */
    public static function validate(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }

    /**
     * @param bool|object $Model
     * @codeCoverageIgnore
     */
    //    private function populate($Model = false)
    //    {
    //        if ($Model !== false && !empty($Model)) {
    //            foreach ($Model as $key => $value) {
    //                if (property_exists($this, $key)) {
    //                    $this->$key = $value;
    //                    //$this[$key] = $value;
    //                }
    //            }
    //        }
    //    }
}
