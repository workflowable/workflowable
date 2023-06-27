<?php

namespace Workflowable\WorkflowEngine\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesWorkflowEngineParameters
{
    protected array $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    abstract public function getRules(): array;

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
