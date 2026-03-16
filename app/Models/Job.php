<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    public static function hasJobsInQueue($queueName): bool
    {
        return static::where('queue', $queueName)->exists();
    }
}
