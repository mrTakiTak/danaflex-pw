<?php

namespace App\Jobs\NavSync;

use App\Actions\NavSync\StartSyncAllModelsJobsAction;
use App\Actions\NavSync\SyncModelDataAction;
use App\Enums\PlaceEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SyncModelDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public $queue = null; // имя очереди

    public $tries = 1;

    public $timeout = 60;

    public function __construct(
        public string $modelClass,
        public PlaceEnum $place
    ) {
        $this->queue = config('local_app.nav_sync_in.queue_prefix.value').$place->value;
    }

    public function handle(): void
    {
        $connectionName = "nav_{$this->place->value}";
        try {
            $lastSyncSetting = StartSyncAllModelsJobsAction::getLastSyncSetting($this->place, $this->modelClass);
            if (is_null($lastSyncSetting->value['lastSyncDateTime'])) {
                throw new \RuntimeException(
                    'Полная синхронизация Модели/Таблицы с Nav не производилась или не завершена. Выполните полную синхронизацию.'
                );
            }
            SyncModelDataAction::syncChanges($this->modelClass, $this->place);

            $lastSyncSetting->value = [
                'lastSyncDateTime' => now(),
            ];
            $lastSyncSetting->save();

        } finally {
            DB::disconnect($connectionName);
        }

    }
}
