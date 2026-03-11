<?php

namespace App\Models\LocalNavSync;

use App\Models\LocalNavSync\BaseClass\LocalNavSyncBaseModel;
use App\Transformers\InData\NavSync\ProductionOrderTransformer;

class ProductionOrder extends LocalNavSyncBaseModel
{

    protected $casts = [
        'date_complete' => 'date'
    ];

    public static array $uniqueKeys = ['place_code', 'no'];

    public static string $transformerClass = ProductionOrderTransformer::class;


}
