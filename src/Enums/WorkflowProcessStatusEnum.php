<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowProcessStatusEnum: int
{
    /**
     * Indicates that we have created the process, but it is not ready to be picked up
     */
    case CREATED = 1;

    /**
     * Indicates that it is ready to be process
     */
    case PENDING = 2;

    /**
     * Indicates that we have dispatched the process to the queue
     */
    case DISPATCHED = 3;

    /**
     * We are actively attempting to process the process
     */
    case RUNNING = 4;

    /**
     * We've paused work on the process
     */
    case PAUSED = 5;

    /**
     * There was an error along the way
     */
    case FAILED = 6;

    /**
     * We've concluded all work for the process
     */
    case COMPLETED = 7;

    /**
     * The workflow process was cancelled
     */
    case CANCELLED = 8;
}
