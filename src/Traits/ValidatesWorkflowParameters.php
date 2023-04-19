<?php

namespace Workflowable\Workflow\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesWorkflowParameters
{
    protected array $parameters = [];

    abstract public function getRules(): array;

    public function hasValidParameters(): bool
    {
        $validator = Validator::make($this->parameters, $this->getRules());

        return $validator->passes();
    }
}
