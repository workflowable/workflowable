<?php

namespace Workflowable\Workflowable\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Workflowable\Workflowable\Exceptions\ParameterException;

class ParameterCast implements CastsAttributes
{
    /**
     * Retrieve and cast a parameter value based on the attribute type.
     *
     * @throws ParameterException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return match (true) {
            $attributes['type'] === 'int' => (int) $value,
            $attributes['type'] === 'float' => (float) $value,
            $attributes['type'] === 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            $attributes['type'] === 'array' => json_decode($value, true),
            $attributes['type'] === 'string' => (string) $value,
            $attributes['type'] === 'null' => null,
            $this->isValidMorphClass(
                $morphClass = $this->getMorphClass($attributes['type'])
            ) => $morphClass::find($value),
            default => throw ParameterException::unsupportedParameterType($attributes['type']),
        };
    }

    public function isValidMorphClass(string $class): bool
    {
        return class_exists($class) && is_subclass_of($class, Model::class);
    }

    public function getMorphClass(string $class): string
    {
        return Arr::get(array_flip(Relation::morphMap() ?: []), $class, $class);
    }

    /**
     * Derive the parameter type and value from the given value.
     *
     * @return array
     *
     * @throws ParameterException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        $type = match (true) {
            $value instanceof Model => $value->getMorphClass(),
            is_int($value) => 'int',
            is_float($value) => 'float',
            is_bool($value) => 'bool',
            is_array($value) => 'array',
            is_null($value) => 'null',
            is_string($value) => 'string',
            default => throw ParameterException::unsupportedParameterType(gettype($value)),
        };

        $value = match (true) {
            $type === 'array' => json_encode($value),
            $value instanceof Model => $value->getKey(),
            default => $value,
        };

        return [
            'value' => $value,
            'type' => $type,
        ];
    }
}
