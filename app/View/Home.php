<?php

namespace App\View;

class Home
{
    public static function view( $path, $variables = [])
    {
        extract($variables);

        require 'resources/' . $path;
    }
}
