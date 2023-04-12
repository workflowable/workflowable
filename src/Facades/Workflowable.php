<?php

namespace Workflowable\Workflowable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Workflowable\Workflowable\Workflowable
 */
class Workflowable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Workflowable\Workflowable\Workflowable::class;
    }
}
