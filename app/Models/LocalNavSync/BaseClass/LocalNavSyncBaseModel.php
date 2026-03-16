<?php

namespace App\Models\LocalNavSync\BaseClass;

use App\Enums\PlaceEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalNavSyncBaseModel extends Model
{
    use SoftDeletes;

    public static bool $isSynced = true;
    public static array $syncPlacesOnly = []; //пример [PlaceEnum::Alabuga,PlaceEnum::Nano];

    public static int $betweenSyncsDelaySeconds = 0;
    protected $guarded = [];

    protected $dateFormat = 'Y-m-d H:i:s.u';
}
