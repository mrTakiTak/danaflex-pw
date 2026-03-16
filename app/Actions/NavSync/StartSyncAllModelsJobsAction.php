<?php

namespace App\Actions\NavSync;

use App\Enums\PlaceEnum;
use App\Jobs\NavSync\SyncModelDataJob;
use App\Models\LocalNavSync\BaseClass\LocalNavSyncBaseModel;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class StartSyncAllModelsJobsAction
{
    public static function handle(PlaceEnum $place)
    {
        $modelsClasses = static::getSyncModelsClasses();

        foreach ($modelsClasses as $modelClass) {
            if (! $modelClass::$isSynced) {
                continue;
            }

            if (! empty($modelClass::$syncPlacesOnly)) {
                if (! in_array($place, $modelClass::$syncPlacesOnly, true)) {
                    continue;
                }
            }
            $lastSyncSetting = static::getLastSyncSetting($place, $modelClass);

            if (! is_null($lastSyncSetting->value['lastSyncDateTime'])) {
                $diffSeconds = Carbon::parse($lastSyncSetting->value['lastSyncDateTime'])->diffInSeconds(Carbon::now());
                if ($diffSeconds < $modelClass::$betweenSyncsDelaySeconds) {
                    continue;
                }
            }
            SyncModelDataJob::dispatch($modelClass, $place);

        }
    }

    public static function getSyncModelsClasses(): array
    {

        $modelsClasses = [];
        $path = app_path('Models');

        foreach (File::allFiles($path) as $file) {
            $class = 'App\\Models\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                $file->getRelativePathname()
            );

            if (class_exists($class) && is_subclass_of($class, LocalNavSyncBaseModel::class)) {
                $modelsClasses[] = $class;
            }

        }

        return $modelsClasses;

    }

    public static function getLastSyncSetting(PlaceEnum $place, string $modelClass)
    {

        $metadata = [
            'placeCode' => $place->value,
            'tableName' => app($modelClass)->getTable(),
        ];
        $setting = Setting::where('name', config('local_app.nav_sync_in.setting_name_for_last_sync.value'))
            ->where('metadata->placeCode', $metadata['placeCode'])
            ->where('metadata->tableName', $metadata['tableName'])
            ->first();

        if (is_null($setting)) {
            $setting = Setting::create([
                'name' => config('local_app.nav_sync_in.setting_name_for_last_sync.value'),
                'metadata' => $metadata,
                'description' => 'Время последней синхронизации данных с Navision. Если null - значит не было полной первоначальной синхронизации данных и ее необходимо провести.',
                'value' => [
                    'lastSyncDateTime' => null,
                ],
            ]);
        }

        return $setting;
    }
}
