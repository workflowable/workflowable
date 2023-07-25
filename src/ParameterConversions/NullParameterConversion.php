<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class NullParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): ?string
    {
        return null;
    }

    public function retrieve(mixed $value, string $type): ?string
    {
        return null;
    }

    public function getParameterConversionType(): string
    {
        return 'null';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return is_null($value);
    }

    public function canRetrieveFromStorage(?string $value, string $type): bool
    {
        return $type === 'null';
    }
}
