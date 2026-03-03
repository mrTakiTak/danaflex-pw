<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __construct()
    {
        if (auth()->check()) {
            config(['app.debug' => true]);
        }
    }

}
