<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivityTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\WorkflowActivityType;

class GetWorkflowActivityTypeImplementationAction extends AbstractAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowActivityException
     */
    public function handle(WorkflowActivityType|int|string $workflowActivityType, array $parameters = []): WorkflowActivityTypeContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_activity_types');

        // If the cache key isn't set, then we need to cache the workflow activity types
        if (! cache()->has($cacheKey)) {
            CacheWorkflowActivityTypeImplementationsAction::make()->handle();
        }

        $workflowActivityTypeId = match (true) {
            $workflowActivityType instanceof WorkflowActivityType => $workflowActivityType->id,
            is_int($workflowActivityType) => $workflowActivityType,
            is_string($workflowActivityType) => WorkflowActivityType::query()
                ->where('alias', $workflowActivityType)
                ->first()
                ?->id,
        };

        // Grab the cached workflow activity types
        $workflowActivityTypeContracts = cache()->get($cacheKey);

        // If the workflow activity type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowActivityTypeContracts[$workflowActivityTypeId])) {
            CacheWorkflowActivityTypeImplementationsAction::make()->handle();
        }

        // If we still haven't found the workflow activity type, then throw an exception
        if (! isset($workflowActivityTypeContracts[$workflowActivityTypeId])) {
            throw WorkflowActivityException::workflowActivityTypeNotRegistered();
        }

        // Return the workflow activity type implementation
        return new $workflowActivityTypeContracts[$workflowActivityTypeId]($parameters);
    }
}
