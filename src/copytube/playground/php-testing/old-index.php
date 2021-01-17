<?php

namespace IndexFile;

// First off, i thought namespaces would be used only for classes (as its easy to call one class yet 2 exist) e.g
/* $myClass = new ClassA(); // could have 2 of these defined */

// but it
// wouldn't work for methods as you have to directly access that class (meaning the method is being pointed from
// a specfic class e.g
/* $myClass->sayHello(); // specifying a class, there's no way a duplicate method could  interfere */

// BUT now im thinking, classes probably wont be named the same as they will be prefixed with model and controller e.g
class LoginController
{
}
class LoginModel
{
}

//BUT
// THEN AGAIN this might be the case for some Libs? So i guess all files could adhere to a App namespace to relate
// all 'app' specific files to the App namespace e.g. App\Controller\Register

// Two classes have a method named the same
namespace App\Controller\ClassA;
class A
{
    public function number()
    {
        return random_int(0, 100);
    }
}

namespace App\Model\ClassA;
class A
{
    public function number()
    {
        return random_int(101, 200);
    }
}

// Now it's impossible to call a method with the same name if we
// specify the class object
use App\Controller\ClassA as ClassAController;
$Controller = new ClassAController\A();
$int = $Controller->number();
echo $int;

echo " : ";

use App\Model\ClassA;
$Model = new ClassA\A();
$int = $Model->number();
echo $int;
