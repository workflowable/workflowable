<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class IntegerParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): ?string
    {
        return $value;
    }

    public function retrieve(string $value, string $type): int
    {
        return (int) $value;
    }

    public function getParameterConversionType(): string
    {
        return 'integer';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_int($value);
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'integer';
    }
}
