<?php

namespace Workflowable\Workflow\Abstracts;

use Illuminate\Support\Collection;

abstract class AbstractData
{
    public function fromArrayOfArrays(array $data): Collection
    {
        $collection = collect([]);

        foreach ($data as $item) {
            $collection->push($this->fromArray($item));
        }

        return $collection;
    }

    abstract public function fromArray(array $data): self;
}
