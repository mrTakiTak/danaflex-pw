<?php

namespace App\Models\NavisionDb\BaseClass\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

trait AutoSyncConnection
{
    protected function newRelatedInstance($class)
    {
        $instance = parent::newRelatedInstance($class);

        // Устанавливаем то же подключение, что и у родителя
        if ($this->getConnectionName()) {
            $instance->setConnection($this->getConnectionName());
        }

        return $instance;
    }

    protected function createRelation(...$parameters): Relation
    {
        $relation = parent::createRelation(...$parameters);

        // Прокидываем подключение в Query Builder
        if ($this->getConnectionName()) {
            $relation->getQuery()->setConnection($this->getConnectionName());
        }

        return $relation;
    }
}
