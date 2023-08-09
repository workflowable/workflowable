<?php

namespace Workflowable\Workflowable\Enums;

enum WorkflowStatusEnum: int
{
    case DRAFT = 1;

    case ACTIVE = 2;

    case DEACTIVATED = 3;

    case ARCHIVED = 4;
}
