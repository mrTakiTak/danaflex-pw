<?php

namespace App\Models\LocalNavSync\BaseClass;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalNavSyncBaseModel extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dateFormat = 'Y-m-d H:i:s.u';
}
