<?php

namespace App\Transformers\InData\NavSync;

use App\Enums\PlaceEnum;
use App\Transformers\InData\NavSync\BaseClass\BaseClassTransformer;

class ProductionOrderTransformer extends BaseClassTransformer
{
    public static array $syncSettings = [
        'navDbTableName' => 'Production Order', // таблица в БД Nav
        'navDbTableNo' => 5405, // номер таблицы в Nav
        'navDbTableFilters' => [], // фильтры подмножества в таблицах типа Different
        'navRowsBatchCount' => 1000,
        'selectFields' => [
            'timestamp',
            'Status',
            'No_',
            'Description',
            'Description 2',
            'Klient',
            'Date_complete',
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
            return $query->where('No_', $deletedKeys[1]);
        }

        return $query->where('no', $deletedKeys[1]);
    }

    public static function transform(array $dataIn, PlaceEnum $place): array
    {

        $dataOut = [
            'timestamp' => bin2hex($dataIn['timestamp']),
            'status_id' => (int) $dataIn['Status'],
            'place_code' => $place->value,
            'no' => $dataIn['No_'],
            'description' => $dataIn['Description'].($dataIn['Description 2']),
            'klient' => arrayValueOrNull($dataIn, 'Klient'),
            'date_complete' => static::transformDateFromDatetime($dataIn['Date_complete']),
        ];

        return $dataOut;
    }
}
