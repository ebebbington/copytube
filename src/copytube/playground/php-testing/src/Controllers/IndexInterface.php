<?php

namespace App\Controllers\Interfaces;

interface IndexInterface
{
    public function select(): void;

    public function update(): void;

    public function insert(): void;

    public function delete(): void;
}
