<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesParameters
{
    protected array $parameters = [];

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    abstract public function getRules(): array;

    /**
     * Evaluates the parameters against the rules.
     *
     * @return bool
     */
    public function hasValidParameters(): bool
    {
        $validator = Validator::make($this->parameters, $this->getRules());

        return $validator->passes();
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
