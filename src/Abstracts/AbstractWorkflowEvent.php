<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use GeneratesNameAndAliases;

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
