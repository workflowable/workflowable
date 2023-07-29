<?php

return [
    /**
     * Workflow events should be registered here by providing a implementation of a WorkflowEventContract.
     *
     * @see \Workflowable\Workflowable\Contracts\WorkflowEventContract
     */
    'workflow_events' => [],

    /**
     * Workflow conditions should be registered here by providing a implementation of a WorkflowConditionContract.
     *
     * @see \Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract
     */
    'workflow_condition_types' => [],

    /**
     * Workflow activities should be registered here by providing an implementation of a workflowActivityTypeContract.
     *
     * @see \Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract
     */
    'workflow_activity_types' => [],

    /**
     * Parameter conversions should be registered here by providing an implementation of a ParameterConversionContract.
     *
     * @see \Workflowable\Workflowable\Contracts\ParameterConversionContract
     */
    'parameter_conversions' => [
        \Workflowable\Workflowable\ParameterConversions\ArrayParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\BooleanParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\DateTimeParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\FloatParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\IntegerParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\ModelParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\NullParameterConversion::class,
        \Workflowable\Workflowable\ParameterConversions\StringParameterConversion::class,
    ],

    /**
     * The queue to use for workflow events.
     */
    'queue' => 'default',

    /**
     * The broadcast channel we should use for all Laravel events for the workflowable package.
     */
    'broadcast_channel' => 'workflowable',

    /**
     * The cache keys used by the workflowable package.
     */
    'cache_keys' => [
        'workflow_events' => 'workflowable:workflow_events',
        'workflow_condition_types' => 'workflowable:workflow_condition_types',
        'workflow_activity_types' => 'workflowable:workflow_activity_types',
    ],
];
