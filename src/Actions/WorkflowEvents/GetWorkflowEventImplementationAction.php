<?php

namespace Workflowable\Workflowable\Actions\WorkflowEvents;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Models\WorkflowEvent;

class GetWorkflowEventImplementationAction extends AbstractAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowEventException
     */
    public function handle(WorkflowEvent|int|string $workflowEvent, array $parameters = []): WorkflowEventContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_events');

        // If the cache key isn't set, then we need to cache the workflow activity types
        if (! cache()->has($cacheKey)) {
            CacheWorkflowEventImplementationsAction::make()->handle();
        }

        $workflowEventId = match (true) {
            $workflowEvent instanceof WorkflowEvent => $workflowEvent->id,
            is_int($workflowEvent) => $workflowEvent,
            is_string($workflowEvent) => WorkflowEvent::query()
                ->where('alias', $workflowEvent)
                ->first()
                ?->id,
        };

        // Grab the cached workflow activity types
        $workflowEventContracts = cache()->get($cacheKey);

        // If the workflow activity type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowEventContracts[$workflowEventId])) {
            CacheWorkflowEventImplementationsAction::make()->handle();
        }

        // If we still haven't found the workflow activity type, then throw an exception
        if (! isset($workflowEventContracts[$workflowEventId])) {
            throw WorkflowEventException::workflowEventNotRegistered();
        }

        // Return the workflow activity type implementation
        return new $workflowEventContracts[$workflowEventId]($parameters);
    }
}
