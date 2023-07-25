<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class StringParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): string
    {
        return $value;
    }

    public function retrieve(string $value, string $type): string
    {
        return $value;
    }

    public function getParameterConversionType(): string
    {
        return 'string';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_string($value);
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'string';
    }
}
