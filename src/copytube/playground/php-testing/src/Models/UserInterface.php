<?php

/**
 * Define what methods a class SHOULD implement. Anything that implements this must declare every function in this file
 */

namespace App\Models\Interfaces;

interface UserInterface
{
    public function insertComment();
}
