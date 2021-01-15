<?php

//include_once 'ClassA.php';
//namespace Controller\ClassB; // Define the name given to this file

// Import the file with namespace 'ClassANamespace' and use the class with 'ClassAClass'
// e.g. use the namespace and class

//use Controller\ClassA\ClassA;

// /classes/MyModel.php
namespace App\Models\Classes; // or remove the "MyModel" bit? as i would do: App\Models\MyModel()

require "DatabaseModel.php";
require "UserInterface.php";
use App\Models\Interfaces\UserInterface;
use App\Models\Classes\DatabaseModel;

class UserModel extends DatabaseModel implements UserInterface
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function insertComment()
    {
        var_dump($this->data);
    }
}

function myFunction()
{
}

const MY_CONST = 0;
