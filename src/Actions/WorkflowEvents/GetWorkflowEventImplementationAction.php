<?php

namespace Workflowable\Workflow\Actions\WorkflowEvents;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Models\WorkflowEvent;

class GetWorkflowEventImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowEventException
     */
    public function handle(WorkflowEvent|int|string $workflowEvent, array $parameters = []): WorkflowEventContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_events');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            (new CacheWorkflowEventImplementationsAction())->handle();
        }

        $workflowEventId = match (true) {
            $workflowEvent instanceof WorkflowEvent => $workflowEvent->id,
            is_int($workflowEvent) => $workflowEvent,
            is_string($workflowEvent) => WorkflowEvent::query()
                ->where('alias', $workflowEvent)
                ->first()
                ?->id,
        };

        // Grab the cached workflow step types
        $workflowEventContracts = cache()->get($cacheKey);

        // If the workflow step type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowEventContracts[$workflowEventId])) {
            $workflowEventContracts = (new CacheWorkflowEventImplementationsAction())->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowEventContracts[$workflowEventId])) {
            throw WorkflowEventException::workflowEventNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowEventContracts[$workflowEventId]($parameters);
    }
}
