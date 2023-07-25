<?php

namespace Workflowable\Workflowable\Contracts;

interface ParameterConversionContract
{
    /**
     * Prepares the value for storage.
     *
     * @return ?string
     */
    public function store(mixed $value): ?string;

    /**
     * Retrieves the value from storage.
     */
    public function retrieve(string $value, string $type): mixed;

    /**
     * Gets the type of parameter.
     */
    public function getParameterConversionType(): string;

    public function canRetrieveFromStorage(string $value, string $type): bool;

    /**
     * Determines if the value can be processed by this conversion for storage.
     */
    public function canPrepareForStorage(mixed $value): bool;
}
