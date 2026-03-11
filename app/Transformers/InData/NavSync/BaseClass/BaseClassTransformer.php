<?php

namespace App\Transformers\InData\NavSync\BaseClass;

use App\Enums\PlaceEnum;
use Carbon\Carbon;

abstract class BaseClassTransformer
{
    abstract public static function transform(array $dataIn, PlaceEnum $place): array;

    public static function transformDateFromDatetime(string $dateTime): ?Carbon
    {

        if ($dateTime === '1753-01-01 00:00:00.000') {

            return null;

        }

        return Carbon::parse($dateTime);
    }
}
