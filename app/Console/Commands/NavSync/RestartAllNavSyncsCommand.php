<?php

namespace App\Console\Commands\NavSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RestartAllNavSyncsCommand extends Command
{
    protected $signature = 'app:restart-all-nav-syncs';

    protected $description = 'Остановка процессов синхронизации по всем площадкам за счет удаления значений из кеша, необходимых для продолжения ';

    public function handle()
    {

        foreach (config('local_app.nav_sync_in.sync_places.value') as $place) {

            Cache::forget(config('local_app.nav_sync_in.sync_process_cache_key_prefix.value').$place->value);
            sleep(2);

            if (app()->isProduction()) {

                // версия для Linux
                pclose(popen("php artisan app:start-nav-sync $place->value > /dev/null 2>&1 &", 'r'));

            } else {
                // версия для Windows
                pclose(popen("start /B php artisan app:start-nav-sync $place->value", 'r'));

            }

        }
    }
}
