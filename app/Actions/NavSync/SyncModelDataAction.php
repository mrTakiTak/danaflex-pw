<?php

namespace App\Actions\NavSync;

use App\Enums\PlaceEnum;
use App\Models\NavisionDb\NavQueueDb;
use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SyncModelDataAction
{
    public static int $navDeletedRowsBatchCount = 100;

    public static function syncChanges(string $modelClass, PlaceEnum $place): void
    {

        $lastTimestampHex = $modelClass::max('timestamp');
        static::syncFromCurrentTimestamp($modelClass, $place, $lastTimestampHex);
        static::syncDeletedOnly($modelClass, $place);

    }

    public static function syncFull(string $modelClass, PlaceEnum $place): void
    {

        $lastSyncSetting = StartSyncAllModelsJobsAction::getLastSyncSetting($place, $modelClass);

        $lastSyncSetting->value = [
            'lastSyncDateTime' => null,
        ];
        $lastSyncSetting->save();

        $modelClass::where('place_code', $place->value)->delete();

        $lastDeletedActionSetting = static::getLastDeletedActionSetting($modelClass, $place);

        $lastDeletedActionSetting->value = [
            'entryNo' => NavQueueDb::on("nav_$place->value")->max('Entry No_'),
        ];
        $lastDeletedActionSetting->save();

        static::syncFromCurrentTimestamp($modelClass, $place, null);
        static::syncDeletedOnly($modelClass, $place);

        $lastSyncSetting->value = [
            'lastSyncDateTime' => now(),
        ];
        $lastSyncSetting->save();

    }

    private static function syncDeletedOnly(string $modelClass, PlaceEnum $place): void
    {

        $transformerClass = $modelClass::$transformerClass;
        $syncSettings = $transformerClass::$syncSettings;
        $navTableNo = $syncSettings['navDbTableNo'];

        $lastDeletedActionSetting = static::getLastDeletedActionSetting($modelClass, $place);

        $connectionNav = DB::connection("nav_$place->value");
        $prefixTableName = $connectionNav->getTablePrefix();
        $tableNameWithPrefix = $prefixTableName.$syncSettings['navDbTableName'];

        do {
            $lastDeletedActionEntryNo = $lastDeletedActionSetting->value['entryNo'];

            $queryNavQueue = NavQueueDb::on("nav_$place->value")
                ->orderBy('Entry No_')
                ->select(['Table No_', 'Action', 'Key Value', 'Entry No_'])
                ->where('Table No_', $navTableNo)
                ->where('Action', NavQueueDb::enumIndex('Action', 'Delete'))
                ->limit(static::$navDeletedRowsBatchCount);

            if (! is_null($lastDeletedActionEntryNo)) {
                $queryNavQueue->where('Entry No_', '>', $lastDeletedActionEntryNo);
            }

            $deletedNavRows = $queryNavQueue->get();

            foreach ($deletedNavRows as $row) {
                $rowsModelQuery = $modelClass::where('place_code', $place->value);

                $rowsModelQuery = $transformerClass::queryDeletedInNavRow($rowsModelQuery, $row->{'Key Value'}, false);

                if ($rowsModelQuery->count() === 0) {
                    continue; // нет в локальной модели - удалять не надо
                }
                // проверяем наличие Nav
                $rowsNavQuery = $connectionNav
                    ->query()
                    ->from(DB::raw("[$tableNameWithPrefix] WITH (NOLOCK)")); // неблокирующее чтение в Nav

                $rowsNavQuery = $transformerClass::queryDeletedInNavRow($rowsNavQuery, $row->{'Key Value'}, true);
                if ($rowsNavQuery->count() === 0) {
                    $rowsModelQuery->delete();
                }

            }

            $lastBatchRow = $deletedNavRows->last();

            if (is_null($lastBatchRow)) {
                break;
            }

            $lastDeletedActionSetting->value =
            [
                'entryNo' => $lastBatchRow->{'Entry No_'},
            ];
            $lastDeletedActionSetting->save();

        } while ($deletedNavRows->count() > 0);

    }

    private static function syncFromCurrentTimestamp(string $modelClass, PlaceEnum $place, ?string $timestampHex = null): void
    {

        $lastTimestampHex = $timestampHex;

        $transformerClass = $modelClass::$transformerClass;
        $syncSettings = $transformerClass::$syncSettings;

        $connectionNav = DB::connection("nav_$place->value");
        $prefixTableName = $connectionNav->getTablePrefix();
        $tableNameWithPrefix = $prefixTableName.$syncSettings['navDbTableName'];
        do {
            $queryNav = $connectionNav
                ->query()
                ->from(DB::raw("[$tableNameWithPrefix] WITH (NOLOCK)")) // неблокирующее чтение в Nav
                ->orderBy('timestamp')
                ->select($syncSettings['selectFields'])
                ->limit($syncSettings['navRowsBatchCount']);
            if (! is_null($lastTimestampHex)) {
                $queryNav->whereRaw("[timestamp] > CONVERT(varbinary(8), 0x{$lastTimestampHex})");
            }

            if (! empty($syncSettings['navDbTableFilters'])) {

                $queryNav->where($syncSettings['navDbTableFilters']);
            }

            $rowsNav = $queryNav->get();

            foreach ($rowsNav as $row) {
                $rowModel = $transformerClass::transform((array) $row, $place);
                $rowModel['deleted_at'] = null;
                $uniqueKeys = Arr::only($rowModel, $modelClass::$uniqueKeys);
                $modelClass::withTrashed()->updateOrCreate($uniqueKeys, $rowModel);
            }

            $lastBatchRow = $rowsNav->last();
            if (is_null($lastBatchRow)) {
                break;
            }
            $lastTimestampHex = bin2hex($lastBatchRow->timestamp);

        } while ($rowsNav->count() > 0);

    }

    private static function getLastDeletedActionSetting(string $modelClass, PlaceEnum $place): Setting
    {
        $metadata = [
            'placeCode' => $place->value,
            'tableName' => app($modelClass)->getTable(),
        ];
        $setting = Setting::where('name', config('local_app.nav_sync_in.setting_name_for_last_sync_delete_action.value'))
            ->where('metadata->placeCode', $metadata['placeCode'])
            ->where('metadata->tableName', $metadata['tableName'])
            ->first();

        if (is_null($setting)) {
            $setting = Setting::create([
                'name' => config('local_app.nav_sync_in.setting_name_for_last_sync_delete_action.value'),
                'metadata' => $metadata,
                'description' => 'Последнее значение Entry No_ таблицы Nav NAV Queue до которого включительно обработаны данные об удаленных записях в Nav',
                'value' => [
                    'entryNo' => null,
                ],
            ]);
        }

        return $setting;

    }
}
