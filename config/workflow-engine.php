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

    'broadcast_channel' => 'workflow-engine',

    'cache_keys' => [
        'workflow_events' => 'workflow-engine:workflow_events',
        'workflow_condition_types' => 'workflow-engine:workflow_condition_types',
        'workflow_step_types' => 'workflow-engine:workflow_step_types',
    ],
];
