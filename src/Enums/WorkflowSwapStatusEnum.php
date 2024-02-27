<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowSwapStatusEnum: int
{
    case Draft = 1;
    case Scheduled = 2;
    case Dispatched = 3;
    case Processing = 4;
    case Completed = 5;

    public static function approvedForDispatch(): array
    {
        return [
            self::Draft,
            self::Scheduled,
        ];
    }

    public static function unapprovedForDispatch(): array
    {
        return [
            self::Dispatched,
            self::Processing,
            self::Completed,
        ];
    }

    public static function approvedForScheduling(): array
    {
        return [
            self::Draft,
            self::Scheduled,
        ];
    }

    public static function unapprovedForScheduling(): array
    {
        return [
            self::Dispatched,
            self::Processing,
            self::Completed,
        ];
    }

    public static function label(WorkflowSwapStatusEnum|int $workflowSwapStatusEnum): string
    {
        return match ($workflowSwapStatusEnum) {
            self::Draft, self::Draft->value => 'Draft',
            self::Scheduled, self::Scheduled->value => 'Scheduled',
            self::Dispatched, self::Dispatched->value => 'Dispatched',
            self::Processing, self::Processing->value => 'Processing',
            self::Completed, self::Completed->value => 'Completed',
            default => throw new \UnhandledMatchError($workflowSwapStatusEnum),
        };
    }
}
