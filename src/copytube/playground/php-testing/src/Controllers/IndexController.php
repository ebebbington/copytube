<?php

namespace App\Controllers\Classes;

require "AbstractController.php";
require "IndexInterface.php";
use App\Controllers\Interfaces\IndexInterface;
require "/Users/edward/Development/environments/php-testing/src/Models/UserModel.php";
use App\Models\Classes\UserModel;

class IndexController extends AbstractController implements IndexInterface
{
    private $view = "";
    private $request = [];
    private $data;

    public function __construct(string $data = "")
    {
        $this->view = "templates/index.html.twig";
        $this->data = $data;
        $this->handleRequest();
    }

    private function handleRequest(): void
    {
        // Say we are adding a comment
        $this->insert();
    }

    public function select(): void
    {
    }

    public function update(): void
    {
    }

    public function delete(): void
    {
    }

    public function insert(): void
    {
        $UserModel = new UserModel($this->data);
        $UserModel->insertComment();
    }
}
