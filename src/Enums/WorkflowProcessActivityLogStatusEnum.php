<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowProcessActivityLogStatusEnum: int
{
    case IN_PROGRESS = 1;
    case SUCCESS = 2;
    case FAILURE = 3;
}
