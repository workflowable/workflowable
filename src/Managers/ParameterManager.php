<?php

namespace Workflowable\Workflowable\Managers;

use Illuminate\Support\Facades\Validator;
use Workflowable\Workflowable\Contracts\ParameterCast;

class ParameterManager
{
    /**
     * @var mixed The raw value of the parameter
     */
    protected mixed $rawValue = null;

    protected ?ParameterCast $cast = null;

    /**
     * @var array Validation rules for the parameter
     */
    protected array $validationRules = [];

    public function __construct()
    {
        $this->validationRules = [];
    }

    /**
     * @param  string|int|float|array  $value
     * @return $this
     *
     * @throws \Exception
     */
    public function setRawValue(mixed $value): self
    {
        $passesValidation = Validator::make(['value' => $value], ['value' => $this->validationRules])->passes();

        if (! $passesValidation) {
            throw new \Exception('Value does not pass validation rules');
        }

        $this->rawValue = $value;

        return $this;
    }

    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setValue(mixed $value): self
    {
        if ($this->cast) {
            $value = $this->cast->set($value);
        }

        $this->setRawValue($value);

        return $this;
    }

    public function getValue(): mixed
    {
        if ($this->cast) {
            return $this->cast->get($this->rawValue);
        }

        return $this->rawValue;
    }

    public function setCast(ParameterCast $cast): self
    {
        $this->cast = $cast;

        return $this;
    }

    /**
     * @return $this
     */
    public function setValidationRules(array $rules): self
    {
        $this->validationRules = $rules;

        return $this;
    }

    public function validate(): bool
    {
        return Validator::make(
            ['value' => $this->rawValue],
            ['value' => $this->validationRules]
        )->passes();
    }
}
