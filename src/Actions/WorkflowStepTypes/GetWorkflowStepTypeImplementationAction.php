<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowStepTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\WorkflowStepType;

class GetWorkflowStepTypeImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowStepException
     */
    public function handle(WorkflowStepType|int|string $workflowStepType, array $parameters = []): WorkflowStepTypeContract
    {
        $cacheKey = config('workflow-engine.cache_keys.workflow_step_types');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            /** @var CacheWorkflowStepTypeImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowStepTypeImplementationsAction::class);
            $cacheAction->handle();
        }

        $workflowStepTypeId = match (true) {
            $workflowStepType instanceof WorkflowStepType => $workflowStepType->id,
            is_int($workflowStepType) => $workflowStepType,
            is_string($workflowStepType) => WorkflowStepType::query()
                ->where('alias', $workflowStepType)
                ->first()
                ?->id,
        };

        // Grab the cached workflow step types
        $workflowStepTypeContracts = cache()->get($cacheKey);

        // If the workflow step type isn't in the cache, then rebuild the cache in-case it was added
        if (! isset($workflowStepTypeContracts[$workflowStepTypeId])) {
            /** @var CacheWorkflowStepTypeImplementationsAction $cacheAction */
            $cacheAction = app(CacheWorkflowStepTypeImplementationsAction::class);
            $workflowStepTypeContracts = $cacheAction->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowStepTypeContracts[$workflowStepTypeId])) {
            throw WorkflowStepException::workflowStepTypeNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowStepTypeContracts[$workflowStepTypeId]($parameters);
    }
}
