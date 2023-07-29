<?php

namespace Workflowable\Workflowable\Tests\Traits;

trait HasParameterConversions
{
    public function setupDefaultConversions(): void
    {
        config()->set('workflowable.parameter_conversions', [
            \Workflowable\Workflowable\ParameterConversions\ArrayParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\BooleanParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\DateTimeParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\FloatParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\IntegerParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\ModelParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\NullParameterConversion::class,
            \Workflowable\Workflowable\ParameterConversions\StringParameterConversion::class,
        ]);
    }
}
