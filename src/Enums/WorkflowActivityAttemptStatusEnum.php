<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowActivityAttemptStatusEnum: int
{
    case SUCCESS = 1;
    case FAILURE = 2;
}
