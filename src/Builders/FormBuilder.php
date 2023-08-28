<?php

namespace Workflowable\Workflowable\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as Validator;
use Workflowable\Workflowable\Fields\Field;

final class FormBuilder
{
    use Macroable;

    /**
     * The given fields needed to build out a workflow component
     */
    protected Collection $fields;

    public function __construct()
    {
        $this->fields = collect([]);
    }

    /**
     * @return $this
     */
    public function make(array $fields): self
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Receives an associative array of field values and sets the values of matching fields on the builder if they exist.
     *
     * @return $this
     */
    public function fill(array $fieldValues): self
    {
        foreach ($fieldValues as $name => $value) {
            if ($this->fields->has($name)) {
                /** @var Field $currentField */
                $currentField = $this->fields->get($name);
                $currentField->setValue($value);
            }
        }

        return $this;
    }

    protected function addField(Field $field, callable $builderCallback = null): self
    {
        if ($builderCallback !== null) {
            $builderCallback($field);
        }

        $this->fields->put($field->getKey(), $field);

        return $this;
    }

    /**
     * Validates the parameters
     *
     * @throws ValidationException
     */
    public function validate(): array
    {
        return $this->getValidator()->validate();
    }

    public function getRules(): array
    {
        return $this->fields->map(function (Field $field) {
            return $field->getRules();
        })->toArray();
    }

    public function getValues(): array
    {
        return $this->fields->map(function (Field $field) {
            return $field->getValue();
        })->toArray();
    }

    public function getValidator(): Validator
    {
        return ValidatorFacade::make($this->getValues(), $this->getRules());
    }

    public function toArray(): array
    {
        return $this->fields->map(function (Field $field) {
            return $field->toArray();
        })->toArray();
    }
}
