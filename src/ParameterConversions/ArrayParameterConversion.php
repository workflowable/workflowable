<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class ArrayParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): string
    {
        return json_encode($value);
    }

    public function retrieve(string $value, string $type): array
    {
        return json_decode($value, true);
    }

    public function getParameterConversionType(): string
    {
        return 'array';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_array($value);
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'array';
    }
}
