<?php

namespace App\Jobs\NavSync;

use App\Actions\NavSync\StartSyncAllModelsJobsAction;
use App\Enums\PlaceEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SyncAllModelsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public $queue = null;

    public $timeout = 10;

    public $tries = 1;

    public function __construct(
        public PlaceEnum $place
    ) {
        $this->queue = config('local_app.nav_sync_in.queue_prefix.value').$place->value;
    }

    public function handle(): void
    {

        $connectionName = "nav_{$this->place->value}";
        try {
            // проверка доступности соединения с ДБ и кредов, чтобы если проблема не плодить failed jоbs для каждой синхронизируемой таблицы.
            DB::connection($connectionName)->select('SELECT 1');
            StartSyncAllModelsJobsAction::handle($this->place);
        } finally {
            DB::disconnect($connectionName);
        }

    }
}
