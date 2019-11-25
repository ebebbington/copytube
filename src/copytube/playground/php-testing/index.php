<?php

/**
 * Possible Namespaces:
 *
 * App\
 *  Index; // Index.php file
 *  Controllers\
 *    Interfaces; // Controller interfaces
 *    Classes; // Controller classes
 *  Models\
 *    Classes; // Model Classes
 *    Interfaces; // Model Interfaces
 */


namespace App\Index;

require 'src/Controllers/IndexController.php';
use App\Controllers\Classes\IndexController;
use http\Client\Curl\User;

/**
 * Say this is the routing for someone, and they have added a comment which lies in the index page
 */
$IndexController = new IndexController('hello there');

class UserModel {

    private $tablename;

    public $name;

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function getByName (string $name = ''): Object
    {
        $this->__construct($name);
        return $this;
    }
}

class UserModel {

    public

    public function __construct(int $id = 0)
    {
        if (isset($id)) {
            $this->id = $id;
            $this->getById();
        }
    }

    private function getById ()
    {
        return DB::table($this->tableName)->where('id', $this->id)->get();
    }

    public function save(array $arrayOfData = []): bool
    {
        $result = DB::table($this->tableName)->create($arrayOfData);
        return isset($result) ? true : false;
    }
}

// When saving i dont want to create an instance of the object, theres no need
// how would i do this without instantiating
$User = new UserModel();
$userSaved = $User->save(['Edward']);

// When getting, i feel i shouldn't have to construct the class with data yet
$User = new UserModel(2); // Now we have our user

// Scenario where i want to get a user
$user = new UserModel;
$user->getByName('edward');
var_dump($user);

// Scenario where i want to save a user (e.g. registration)
$user = new UserModel('Harry');
$user->save();

/*
 * -> gets a message from a model and saves the val in the controller
 * -> sends that val to the extended abstract controller which will render
 *
 * e.g.
 * -> $Model->getMessage
 * -> $this->>render($message)
 */
