<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowSwapStatusEnum: int
{
    case Draft = 1;
    case Scheduled = 2;
    case Dispatched = 3;
    case Processing = 4;
    case Completed = 5;
}
