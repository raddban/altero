<?php
namespace App\Controllers;
use App\Models\Model;

class Controller
{
    public function index()
    {
        return header('location: /');
    }
    public function contact()
    {
        return Model::model();
    }
}