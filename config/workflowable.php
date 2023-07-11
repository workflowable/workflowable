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
     * Workflow steps should be registered here by providing an implementation of a WorkflowStepTypeContract.
     *
     * @see \Workflowable\Workflowable\Contracts\WorkflowStepTypeContract
     */
    'workflow_step_types' => [],

    'broadcast_channel' => 'workflowable',

    'cache_keys' => [
        'workflow_events' => 'workflowable:workflow_events',
        'workflow_condition_types' => 'workflowable:workflow_condition_types',
        'workflow_step_types' => 'workflowable:workflow_step_types',
    ],
];