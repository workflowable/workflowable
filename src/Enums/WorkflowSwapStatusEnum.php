<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowSwapStatusEnum: int
{
    case Draft = 1;
    case Pending = 2;
    case Processing = 3;
    case Completed = 4;
}
