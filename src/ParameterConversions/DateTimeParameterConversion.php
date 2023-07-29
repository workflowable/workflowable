<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Contracts\ParameterConversionContract;

class DateTimeParameterConversion implements ParameterConversionContract
{
    public function store(mixed $value): string
    {
        return $value->format('c');
    }

    public function retrieve(mixed $value, string $type): \DateTimeInterface
    {
        return Carbon::parse($value);
    }

    public function getParameterConversionType(): string
    {
        return 'datetime';
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return $value instanceof \DateTimeInterface;
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return $type === 'datetime';
    }
}
