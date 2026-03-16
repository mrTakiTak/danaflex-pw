<?php

namespace App\Console\Commands\NavSync;

use App\Actions\NavSync\StartSyncAllModelsJobsAction;
use App\Actions\NavSync\SyncModelDataAction;
use App\Enums\PlaceEnum;
use Illuminate\Console\Command;

class StartNavFullSyncCommand extends Command
{
    /***
     * Пример запуска из командной строки: php artisan app:start-nav-full-sync zao production_orders
    ***/
    protected $signature = 'app:start-nav-full-sync {placeCode} {tableName}';

    protected $description = 'Старт полной синхронизации таблицы с Nav';

    public function handle()
    {

        $place = PlaceEnum::from($this->argument('placeCode'));
        $tableName = $this->argument('tableName');

        $syncModelsClasses = StartSyncAllModelsJobsAction::getSyncModelsClasses();
        $modelClassToSync = null;

        foreach ($syncModelsClasses as $modelClass) {
            if (app($modelClass)->getTable() === $tableName) {
                $modelClassToSync = $modelClass;
                break;
            }
        }

        if (is_null($modelClassToSync)) {
            throw new \RuntimeException(
                'По имени Таблицы не найден класс синхронизируемой с Nav модели'
            );
        }

        SyncModelDataAction::syncFull($modelClassToSync, $place);

    }
}
