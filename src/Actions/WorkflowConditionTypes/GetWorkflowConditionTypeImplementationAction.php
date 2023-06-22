<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract;
use Workflowable\WorkflowEngine\Exceptions\WorkflowConditionException;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;

class GetWorkflowConditionTypeImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     */
    public function handle(WorkflowConditionType|int|string $workflowConditionType, array $parameters = []): WorkflowConditionTypeContract
    {
        $cacheKey = config('workflow-engine.cache_keys.workflow_condition_types');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            /** @var CacheWorkflowConditionTypeImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowConditionTypeImplementationsAction::class);
            $cacheAction->handle();
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
            /** @var CacheWorkflowConditionTypeImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowConditionTypeImplementationsAction::class);
            $workflowConditionTypeContracts = $cacheAction->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowConditionTypeContracts[$workflowConditionTypeId])) {
            throw WorkflowConditionException::workflowConditionTypeNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowConditionTypeContracts[$workflowConditionTypeId]($parameters);
    }
}
