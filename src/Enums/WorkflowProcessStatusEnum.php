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

    public static function active(): array
    {
        return [
            WorkflowProcessStatusEnum::PENDING,
            WorkflowProcessStatusEnum::FAILED,
            WorkflowProcessStatusEnum::PAUSED,
            WorkflowProcessStatusEnum::CREATED,
            WorkflowProcessStatusEnum::DISPATCHED,
            WorkflowProcessStatusEnum::RUNNING,
        ];
    }

    public static function running(): array
    {
        return [
            WorkflowProcessStatusEnum::DISPATCHED,
            WorkflowProcessStatusEnum::RUNNING,
        ];
    }

    public static function inactive(): array
    {
        return [
            WorkflowProcessStatusEnum::CANCELLED,
            WorkflowProcessStatusEnum::COMPLETED,
        ];
    }

    public static function label(WorkflowProcessStatusEnum|int $statusEnum): string
    {
        return match ($statusEnum) {
            WorkflowProcessStatusEnum::PENDING, WorkflowProcessStatusEnum::PENDING->value => 'Pending',
            WorkflowProcessStatusEnum::FAILED, WorkflowProcessStatusEnum::FAILED->value => 'Failed',
            WorkflowProcessStatusEnum::PAUSED, WorkflowProcessStatusEnum::PAUSED->value => 'Paused',
            WorkflowProcessStatusEnum::CREATED, WorkflowProcessStatusEnum::CREATED->value => 'Created',
            WorkflowProcessStatusEnum::DISPATCHED, WorkflowProcessStatusEnum::DISPATCHED->value => 'Dispatched',
            WorkflowProcessStatusEnum::RUNNING, WorkflowProcessStatusEnum::RUNNING->value => 'Running',
            WorkflowProcessStatusEnum::CANCELLED, WorkflowProcessStatusEnum::CANCELLED->value => 'Cancelled',
            WorkflowProcessStatusEnum::COMPLETED, WorkflowProcessStatusEnum::COMPLETED->value => 'Completed',
            default => throw new \UnhandledMatchError($statusEnum),
        };
    }
}
