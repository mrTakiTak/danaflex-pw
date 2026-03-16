<?php

namespace App\Models\LocalNavSync\Vocabs\Different;

use App\Models\LocalNavSync\BaseClass\LocalNavSyncBaseModel;
use App\Transformers\InData\NavSync\Vocabs\Different\CylinderTransformer;

class Cylinder extends LocalNavSyncBaseModel
{

    protected $table = 'vocab_cylinders';

    protected $casts = [

    ];

    public static array $uniqueKeys = ['place_code', 'code'];

    public static array $syncPlacesOnly =[];

    public static int $betweenSyncsDelaySeconds = 0;

    public static string $transformerClass = CylinderTransformer::class;


}
