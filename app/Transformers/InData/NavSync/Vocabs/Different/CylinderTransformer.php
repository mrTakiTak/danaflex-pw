<?php

namespace App\Transformers\InData\NavSync\Vocabs\Different;

use App\Enums\PlaceEnum;
use App\Transformers\InData\NavSync\BaseClass\BaseClassTransformer;

class CylinderTransformer extends BaseClassTransformer
{
    public static array $syncSettings = [
        'navDbTableName' => 'different', // таблица в БД Nav
        'navDbTableNo' => 50001, // номер таблицы в Nav
        'navDbTableFilters' => [ // фильтры подмножества в таблицах типа Different
            ['type', '=', 'CYLINDER'],
            ['code', '<>', ''],
        ],
        'navRowsBatchCount' => 1000,
        'selectFields' => [
            'timestamp',
            'type',
            'code',
            'liniatura',
            'comment',
            'status',
        ],

    ];

    /***
     * Запрос для поиска удаляемой строки по ключам из таблицы NAV Queue (Nav) в синхронизирумой модели
     * $deletedKeyValue - поле 'Key Value' из удаляемой строки таблицы NAV Queue (Nav)
     * для поиска удаленного значения в синхронизируемой локальной таблице.
     */
    public static function queryDeletedInNavRow(\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query, string $deletedKeyValue, bool $isNavQuery): \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
    {
        $deletedKeys = array_map('trim', explode(',', $deletedKeyValue));

        if ($isNavQuery) {
            return $query->where('type', 'CYLINDER')->where('code', $deletedKeys[1]);
        }

        if ($deletedKeys[0] !== 'CYLINDER') {

            // возвращаем "пустой запрос" - удаление в локальной модели будет проигнорировано
            return $query->whereRaw('1 = -1');
        }

        return $query->where('code', $deletedKeys[1]);
    }

    public static function transform(array $dataIn, PlaceEnum $place): array
    {

        $dataOut = [
            'timestamp' => bin2hex($dataIn['timestamp']),
            'place_code' => $place->value,
            'code' => $dataIn['code'],
            'comment' => $dataIn['comment'],
            'circumference' => (int) $dataIn['liniatura'],
            'status_id' => (int) $dataIn['status'],
        ];

        return $dataOut;
    }
}
