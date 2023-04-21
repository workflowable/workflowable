<?php

namespace Workflowable\Workflow\Actions\WorkflowStepTypes;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStepType;

class GetWorkflowStepTypeImplementationAction
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowStepException
     */
    public function handle(WorkflowStepType|int|string $workflowStepType, array $parameters = []): WorkflowStepTypeContract
    {
        $cacheKey = config('workflowable.cache_keys.workflow_step_types');

        // If the cache key isn't set, then we need to cache the workflow step types
        if (! cache()->has($cacheKey)) {
            (new CacheWorkflowStepTypeImplementationsAction())->handle();
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
            $workflowStepTypeContracts = (new CacheWorkflowStepTypeImplementationsAction)->handle();
        }

        // If we still haven't found the workflow step type, then throw an exception
        if (! isset($workflowStepTypeContracts[$workflowStepTypeId])) {
            throw WorkflowStepException::workflowStepTypeNotRegistered();
        }

        // Return the workflow step type implementation
        return new $workflowStepTypeContracts[$workflowStepTypeId]($parameters);
    }
}
