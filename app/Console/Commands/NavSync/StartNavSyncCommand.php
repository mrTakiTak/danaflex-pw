<?php

namespace App\Console\Commands\NavSync;

use App\Enums\PlaceEnum;
use App\Jobs\NavSync\SyncAllModelsJob;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StartNavSyncCommand extends Command
{
    /***
     * Пример запуска из командной строки: php artisan app:start-nav-sync zao
     * Cоздаются job. Требуется запуск воркера по каждой  площадке Nav.
     * Пример запуска воркера из командной строки: php artisan queue:work --queue=nav_sync_in_zao
     * Для прерывания бесконечного цикла - делаем очистку кеша).
     */
    protected $signature = 'app:start-nav-sync {placeCode}';

    protected $description = 'Старт синхронизации данных с по указанной площадке через создание job';

    public function handle()
    {

        $place = PlaceEnum::from($this->argument('placeCode'));

        $lock = base_path("storage/app/lock/nav_sync.$place->value.lock");
        $dir = dirname($lock);
        if (! file_exists($dir)) {

            mkdir($dir, 0755, true);
        }
        $f = fopen($lock, 'w') or exit;
        if (flock($f, LOCK_EX | LOCK_NB)) {

            $cacheKey = config('local_app.nav_sync_in.sync_process_cache_key_prefix.value').$place->value;

            Cache::put($cacheKey, true);
            while (Cache::get($cacheKey)) {
                if (! Job::hasJobsInQueue(config('local_app.nav_sync_in.queue_prefix.value').$place->value)) {
                    SyncAllModelsJob::dispatch($place);
                }
                sleep(1);
            }

        }

    }
}
