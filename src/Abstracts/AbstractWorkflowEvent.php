<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Concerns\GeneratesHumanReadableNameForClass;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use GeneratesHumanReadableNameForClass;

    protected FormManager $formManager;

    public function __construct($inputTokens = [])
    {
        $this->formManager = $this->makeForm()->fill($inputTokens);
    }

    public function getTokens(): array
    {
        return $this->formManager->getValues();
    }

    public function hasValidTokens(): bool
    {
        return $this->formManager->isValid();
    }

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
