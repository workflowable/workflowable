<?php

namespace Workflowable\Workflowable\Abstracts;

use Illuminate\Support\Facades\App;
use Mockery;
use Mockery\MockInterface;

abstract class AbstractAction
{
    public static function make(): static
    {
        return App::make(static::class);
    }

    public static function fake(\Closure $closure): MockInterface
    {
        $mock = Mockery::mock(static::class, $closure)->makePartial();
        App::instance(static::class, $mock);

        return $mock;
    }
}
