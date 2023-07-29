<?php

namespace Workflowable\Workflowable\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflowable\Contracts\ParameterConversionContract;
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
        /** @var array<ParameterConversionContract> $conversionFQNs */
        $conversionFQNs = config('workflowable.parameter_conversions') ?? [];

        foreach ($conversionFQNs as $conversionFQN) {
            /** @var ParameterConversionContract $conversion */
            $conversion = app($conversionFQN);

            $canPerformConversion = $conversion->canRetrieveFromStorage($value, $attributes['type']);
            if ($canPerformConversion) {
                return $conversion->retrieve($value, $attributes['type']);
            }
        }

        throw ParameterException::unableToRetrieveParameterForType($attributes['type']);
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
        /** @var array<ParameterConversionContract> $conversionFQNs */
        $conversionFQNs = config('workflowable.parameter_conversions') ?? [];

        foreach ($conversionFQNs as $conversionFQN) {
            /** @var ParameterConversionContract $conversion */
            $conversion = app($conversionFQN);

            $canPrepareForStorage = $conversion->canPrepareForStorage($value);
            if ($canPrepareForStorage) {
                return [
                    'value' => $conversion->store($value),
                    'type' => $conversion->getParameterConversionType(),
                ];
            }
        }

        throw ParameterException::unableToPrepareParameterForStorage();
    }
}
