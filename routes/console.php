<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('nav_sync_in_start', function () {
    foreach (config('local_app.nav_sync_in.sync_places.value') as $place) {
        $this->call('app:start-nav-sync', ['placeCode' => $place->value]);
    }
})->purpose('nav sync in start')->everyMinute();
