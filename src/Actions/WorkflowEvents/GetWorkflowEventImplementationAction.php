<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowEvents;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Exceptions\WorkflowEventException;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;

class GetWorkflowEventImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowEventException
     */
    public function handle(WorkflowEvent|int|string $workflowEvent, array $parameters = []): WorkflowEventContract
    {
        $cacheKey = config('workflow-engine.cache_keys.workflow_events');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            /** @var CacheWorkflowEventImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowEventImplementationsAction::class);
            $cacheAction->handle();
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
            /** @var CacheWorkflowEventImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowEventImplementationsAction::class);
            $workflowEventContracts = $cacheAction->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowEventContracts[$workflowEventId])) {
            throw WorkflowEventException::workflowEventNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowEventContracts[$workflowEventId]($parameters);
    }
}
