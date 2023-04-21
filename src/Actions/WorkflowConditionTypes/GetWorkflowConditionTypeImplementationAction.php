<?php

namespace Workflowable\Workflow\Actions\WorkflowConditionTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowConditionType;

class GetWorkflowConditionTypeImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     */
    public function handle(WorkflowConditionType|int|string $workflowConditionType, array $parameters = []): WorkflowConditionTypeContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_condition_types');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            (new CacheWorkflowConditionTypeImplementationsAction)->handle();
        }

        $workflowConditionTypeId = match (true) {
            $workflowConditionType instanceof WorkflowConditionType => $workflowConditionType->id,
            is_int($workflowConditionType) => $workflowConditionType,
            is_string($workflowConditionType) => WorkflowConditionType::query()
                ->where('alias', $workflowConditionType)
                ->first()
                ?->id,
        };

        // Grab the cached workflow step types
        $workflowConditionTypeContracts = cache()->get($cacheKey);

        // If the workflow step type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowConditionTypeContracts[$workflowConditionTypeId])) {
            $workflowConditionTypeContracts = (new CacheWorkflowConditionTypeImplementationsAction)->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowConditionTypeContracts[$workflowConditionTypeId])) {
            throw WorkflowConditionException::workflowConditionTypeNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowConditionTypeContracts[$workflowConditionTypeId]($parameters);
    }
}
