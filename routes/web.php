<?php

use Illuminate\Support\Facades\Route;
use Danaflex\DWH\Enums\PlaceEnum;

Route::middleware('auth')->get('/', function () {
    return \Inertia\Inertia::render('index');
})->name('index');

Route::middleware(['auth'])->get(
    '/403',
    function () {
        return \Inertia\Inertia::render('Error/ForbiddenPage');
    }
);


