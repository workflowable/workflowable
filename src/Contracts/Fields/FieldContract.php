<?php

namespace Workflowable\Workflowable\Contracts\Fields;

use Closure;
use Workflowable\Workflowable\Fields\Field;

interface FieldContract
{
    public function __construct(string $label, string $key);

    /**
     * The key for the field
     */
    public function getKey(): string;

    /**
     * The label to be displayed for the field
     */
    public function getLabel(): string;

    /**
     * Set the value of the field
     *
     * @return $this
     */
    public function setValue(mixed $value): self;

    /**
     * Get the value of the field
     */
    public function getValue(): mixed;

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array<string, mixed>
     */
    public function getMetaData(): array;

    /**
     * Set additional meta information for the element.
     *
     * @return $this
     */
    public function withMetaData(array $metaData): self;

    /**
     * Used to validate the data for the field.
     *
     * @param array|string|Closure $rules
     * @return $this
     */
    public function rules(array|string|Closure $rules): self;

    /**
     * Get the validation rules for the field
     */
    public function getRules(): array|string|Closure;

    /**
     * Set the component to be used when rendering the field
     *
     * @return $this
     */
    public function helpText(string $helpText): self;

    /**
     * Get the help text for the field
     */
    public function getHelpText(): ?string;

    /**
     * Convert the field to an array.
     */
    public function toArray(): array;
}
