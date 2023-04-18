<?php

namespace Workflowable\Workflow\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesWorkflowParameters
{
    abstract public function getRules(): array;

    public function hasValidParameters(array $parameters): bool
    {
        $validator = Validator::make($parameters, $this->getRules());

        return $validator->passes();
    }
}
