<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class FloatParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): ?string
    {
        return $value;
    }

    public function retrieve(string $value, string $type): float
    {
        return (float) $value;
    }

    public function getParameterConversionType(): string
    {
        return 'float';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_float($value);
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'float';
    }
}
