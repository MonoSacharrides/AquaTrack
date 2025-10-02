<?php

// app/Traits/LogsActivity.php
namespace App\Traits;

use App\Models\Activity;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getModelEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    protected static function getModelEvents()
    {
        return ['created', 'updated', 'deleted'];
    }

    protected function logActivity($event)
    {
        $description = $this->getActivityDescription($event);

        Activity::log(
            $event,
            $description,
            $this,
            $this->getActivityProperties($event)
        );
    }

    protected function getActivityDescription($event)
    {
        $modelName = class_basename($this);

        return match ($event) {
            'created' => "New {$modelName} was created",
            'updated' => "{$modelName} was updated",
            'deleted' => "{$modelName} was deleted",
            default => "{$modelName} was {$event}",
        };
    }

    protected function getActivityProperties($event)
    {
        return $event === 'updated'
            ? ['changes' => $this->getChanges()]
            : ['attributes' => $this->attributesToArray()];
    }
}
