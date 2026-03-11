<?php
declare(strict_types=1);

namespace App\Models\NavisionDb\BaseClass;

use App\Models\NavisionDb\BaseClass\Traits\AutoSyncConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NavisionBaseModel extends Model
{
    use AutoSyncConnection;

    public $timestamps = false;

    protected static array $enums = [];

    protected static function booted()
    {

        static::addGlobalScope('nolock', function (Builder $builder) {
            /***
             * Чтение NOLOCK для исключения блокировки таблиц
             */

            // Получаем модель
            $model = $builder->getModel();

            // Получаем имя таблицы без префикса
            $table = $model->getTable();

            // Получаем активное соединение
            $connection = DB::connection($model->getConnectionName());

            // Получаем префикс из конфигурации
            $prefix = $connection->getTablePrefix();

            // Формируем имя таблицы с префиксом
            $tableWithPrefix = $prefix.$table;

            // Применяем NOLOCK с префиксированным именем таблицы
            $builder->fromRaw("[$tableWithPrefix] WITH (NOLOCK)");
        });

    }

   public static function enumIndex(string $fieldName, string $enumValue): int
    {
        $fieldEnumValues = static::$enums[$fieldName];

        return array_search($enumValue, $fieldEnumValues);
        // $enumValue не найдено в массиве, то будет исключение (см. declare(strict_types=1) вначале данного файла); , т.к. array_search вернет true, а это не int
    }
}
