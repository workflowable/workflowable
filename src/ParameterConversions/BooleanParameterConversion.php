<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class BooleanParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): string
    {
        return ''.$value;
    }

    public function retrieve(mixed $value, string $type): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getParameterConversionType(): string
    {
        return 'boolean';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_bool($value);
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'boolean';
    }
}
