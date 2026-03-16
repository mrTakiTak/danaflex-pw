<?php

namespace App\Console\Commands\NavSync;

use App\Actions\NavSync\StartSyncAllModelsJobsAction;
use App\Actions\NavSync\SyncModelDataAction;
use App\Enums\PlaceEnum;
use Illuminate\Console\Command;

/***
 * Поочередный запуск полной синхронизации с Nav по определенной площадке Таблиц/Моделей.
 * ТОЛЬКО ТЕХ ПОЛНАЯ СИНХРОНИЗАЦИИ КОТОРЫХ НЕ ПРОИЗВОДИЛАСЬ НИКОДА!
 * Пример запуска из командной строки: php artisan app:start-all-nav-full-sync zao
 ***/
class StartAllNavFullSyncCommand extends Command
{
    protected $signature = 'app:start-all-nav-full-sync {placeCode}';

    protected $description = 'Поочередный запуск полной синхронизации с Nav по определенной площадке Таблиц/Моделей. ТОЛЬКО ТЕХ ПОЛНАЯ СИНХРОНИЗАЦИИ КОТОРЫХ НЕ ПРОИЗВОДИЛАСЬ НИКОДА! ';

    public function handle()
    {

        $place = PlaceEnum::from($this->argument('placeCode'));

        $syncModelsClasses = StartSyncAllModelsJobsAction::getSyncModelsClasses();

        foreach ($syncModelsClasses as $modelClass) {
            $lastSyncSetting = StartSyncAllModelsJobsAction::getLastSyncSetting($place, $modelClass);
            if (is_null($lastSyncSetting->value['lastSyncDateTime'])) {
                $tableName = app($modelClass)->getTable();
                echo "$tableName ($modelClass) ... ";
                SyncModelDataAction::syncFull($modelClass, $place);
                echo " Успешно\n";
            }
        }



    }
}
