<?php

namespace Workflowable\Workflowable\Concerns;

use Workflowable\Forms\Form;

trait ValidatesWorkflowParameters
{
    protected Form $form;

    public function __construct(array $parameters = [])
    {
        $this->form = $this->makeForm(new Form());
        $this->form->fill($parameters);
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

    abstract public function makeForm(Form $form): Form;
}
