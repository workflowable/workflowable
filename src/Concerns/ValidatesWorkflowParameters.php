<?php

namespace Workflowable\Workflowable\Concerns;

use Workflowable\Workflowable\Builders\FormBuilder;

trait ValidatesWorkflowParameters
{
    protected FormBuilder $form;

    public function __construct(array $parameters = [])
    {
        $formBuilder = new FormBuilder();
        $this->form = $this->makeForm($formBuilder)->fill($parameters);
    }

    /**
     * Evaluates the parameters against the rules.
     */
    public function hasValidParameters(): bool
    {
        return $this->form->getValidator()->passes();
    }

    public function getParameters(): array
    {
        return $this->form->getValues();
    }

    abstract public function makeForm(FormBuilder $form): FormBuilder;
}
