<?php

namespace Workflowable\Workflowable\ParameterConversions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Workflowable\Workflowable\Contracts\ParameterConversionContract;
use Workflowable\Workflowable\Exceptions\ParameterException;

class ModelParameterConversion implements ParameterConversionContract
{
    protected ?string $morphType = null;

    public function store(mixed $value): string
    {
        $this->morphType = $value->getMorphClass();

        return $value->getKey();
    }

    public function retrieve(mixed $value, string $type): ?Model
    {
        $morphMapKey = Str::after($type, 'model:');
        $morphClassFQN = Arr::get(array_flip(Relation::morphMap() ?: []), $morphMapKey, $morphMapKey);

        if (class_exists($morphClassFQN) && is_subclass_of($morphClassFQN, Model::class)) {
            return $morphClassFQN::query()->findOrFail($value);
        }

        throw ParameterException::invalidModel($type);
    }

    public function getParameterConversionType(): string
    {
        if (is_null($this->morphType)) {
            throw new ParameterException('Unable to get parameter conversion type.');
        }

        return 'model:'.$this->morphType;
    }

    public function canPrepareForStorage(mixed $value): bool
    {
        return $value instanceof Model;
    }

    public function canRetrieveFromStorage(string $value, string $type): bool
    {
        return Str::startsWith($type, 'model:');
    }
}
