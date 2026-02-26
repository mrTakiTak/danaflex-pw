<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/', function () {
    return \Inertia\Inertia::render('index');
})->name('index');
