<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory as LaravelHasFactory;

trait HasFactory
{
    use LaravelHasFactory;

    public static function newFactory(): Factory
    {
        return app('Workflowable\\Workflowable\\Database\\Factories\\'.class_basename(static::class).'Factory');
    }
}
