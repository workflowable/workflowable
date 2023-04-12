<?php

return [
    /**
     * Workflow events should be registered here by providing a implementation of a WorkflowEventContract.
     *
     * @see \Workflowable\Workflow\Contracts\WorkflowEventContract
     */
    'workflow_events' => [],

    /**
     * Workflow conditions should be registered here by providing a implementation of a WorkflowConditionContract.
     *
     * @see \Workflowable\Workflow\Contracts\WorkflowConditionContract
     */
    'workflow_conditions' => [],

    /**
     * Workflow actions should be registered here by providing an implementation of a WorkflowActionContract.
     *
     * @see \Workflowable\Workflow\Contracts\WorkflowActionContract
     */
    'workflow_actions' => [],

    /**
     * The minimum number of seconds between when a workflow run is attempted and when it is retried.
     *
     * TODO: Implement this functionality in the WorkflowRunnerJob
     */
    'delay_between_workflow_run_attempts' => 60, // 60 seconds

    'broadcast_channel' => 'workflowable',
];
