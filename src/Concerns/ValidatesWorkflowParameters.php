<?php

namespace Workflowable\Workflowable\Concerns;

use Illuminate\Support\Facades\Validator;

trait ValidatesWorkflowParameters
{
    protected array $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    abstract public function getRules(): array;

    /**
     * Evaluates the parameters against the rules.
     */
    public function hasValidParameters(): bool
    {
        $validator = Validator::make($this->parameters, $this->getRules());

        return $validator->passes();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
