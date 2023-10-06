<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditionTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Exceptions\WorkflowConditionException;
use Workflowable\Workflowable\Models\WorkflowConditionType;

class GetWorkflowConditionTypeImplementationAction extends AbstractAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     */
    public function handle(WorkflowConditionType|int|string $workflowConditionType, array $parameters = []): WorkflowConditionTypeContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_condition_types');

        // If the cache key isn't set, then we need to cache the workflow activity types
        if (! cache()->has($cacheKey)) {
            CacheWorkflowConditionTypeImplementationsAction::make()->handle();
        }

        $workflowConditionTypeId = match (true) {
            $workflowConditionType instanceof WorkflowConditionType => $workflowConditionType->id,
            is_int($workflowConditionType) => $workflowConditionType,
            is_string($workflowConditionType) => WorkflowConditionType::query()
                ->where('alias', $workflowConditionType)
                ->first()
                ?->id,
        };

        // Grab the cached workflow activity types
        $workflowConditionTypeContracts = cache()->get($cacheKey);

        // If the workflow activity type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowConditionTypeContracts[$workflowConditionTypeId])) {
            CacheWorkflowConditionTypeImplementationsAction::make()->handle();
        }

        // If we still haven't found the workflow activity type, then throw an exception
        if (! isset($workflowConditionTypeContracts[$workflowConditionTypeId])) {
            throw WorkflowConditionException::workflowConditionTypeNotRegistered();
        }

        // Return the workflow activity type implementation
        return new $workflowConditionTypeContracts[$workflowConditionTypeId]($parameters);
    }
}
