<?php

return [
    /**
     * Workflow events should be registered here by providing a implementation of a WorkflowEventContract.
     *
     * @see \Workflowable\WorkflowEngine\Contracts\WorkflowEventContract
     */
    'workflow_events' => [],

    /**
     * Workflow conditions should be registered here by providing a implementation of a WorkflowConditionContract.
     *
     * @see \Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract
     */
    'workflow_condition_types' => [],

    /**
     * Workflow steps should be registered here by providing an implementation of a WorkflowStepTypeContract.
     *
     * @see \Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract
     */
    'workflow_step_types' => [],

    /**
     * The minimum number of seconds between when a workflow run is attempted and when it is retried.
     *
     * TODO: Implement this functionality in the WorkflowRunnerJob
     */
    'delay_between_workflow_run_attempts' => 60, // 60 seconds

    'broadcast_channel' => 'workflowable',

    'cache_keys' => [
        'workflow_events' => 'workflowable:workflow_events',
        'workflow_condition_types' => 'workflowable:workflow_condition_types',
        'workflow_step_types' => 'workflowable:workflow_step_types',
    ],
];
