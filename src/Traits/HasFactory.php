<?php

namespace Workflowable\Workflow\Traits;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory as LaravelHasFactory;

trait HasFactory
{
    use LaravelHasFactory;

    public static function newFactory(): Factory
    {
        return app('Workflowable\\Workflow\\Database\\Factories\\'.class_basename(static::class).'Factory');
    }
}
