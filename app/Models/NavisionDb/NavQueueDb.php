<?php

namespace App\Models\NavisionDb;

use App\Models\NavisionDb\BaseClass\NavisionBaseModel;

class NavQueueDb extends NavisionBaseModel
{
    protected $table = 'NAV Queue';

    public static array $enums = [ //fieldName => values
        'Action' => ['Insert','Update','Delete']
    ];





}
