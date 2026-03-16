<?php

namespace App\Console\Commands\NavSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StopAllNavSyncsCommand extends Command
{
    protected $signature = 'app:stop-all-nav-syncs';

    protected $description = 'Остановка процессов синхронизации по всем площадкам за счет удаления значений из кеша, необходимых для продолжения ';

    public function handle(): void
    {

        foreach (config('local_app.nav_sync_in.sync_places.value') as $place) {
            Cache::forget(config('local_app.nav_sync_in.sync_process_cache_key_prefix.value').$place->value);
        }
    }
}
