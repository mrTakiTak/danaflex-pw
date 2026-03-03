<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    public function scopePayloadContains(Builder $query, string $substring): Builder
    {
        return $query->where('payload', 'ILIKE',"%$substring%");
    }
}
