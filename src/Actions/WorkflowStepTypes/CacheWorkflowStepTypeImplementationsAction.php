<?php

namespace Workflowable\Workflow\Actions\WorkflowStepTypes;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEventWorkflowStepType;
use Workflowable\Workflow\Models\WorkflowStepType;

class CacheWorkflowStepTypeImplementationsAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        $this->shouldBustCache = true;

        return $this;
    }

    public function handle(): array
    {
        $key = config('workflow-engine.cache_keys.workflow_step_types');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflow-engine.workflow_step_types') ?? [] as $workflowStepTypeContract) {
                /** @var WorkflowStepTypeContract $workflowStepTypeContract */
                $workflowStepTypeContract = app($workflowStepTypeContract);

                $workflowStepType = WorkflowStepType::query()
                    ->firstOrCreate([
                        'alias' => $workflowStepTypeContract->getAlias(),
                    ], [
                        'name' => $workflowStepTypeContract->getName(),
                        'alias' => $workflowStepTypeContract->getAlias(),
                    ]);

                if (! empty($workflowStepTypeContract->getWorkflowEventAliases())) {
                    foreach ($workflowStepTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                        $workflowEventId = WorkflowEvent::query()
                            ->where('alias', $workflowEventAlias)
                            ->firstOrFail()
                            ->id;

                        WorkflowEventWorkflowStepType::query()->firstOrCreate([
                            'workflow_step_type_id' => $workflowStepType->id,
                            'workflow_event_id' => $workflowEventId,
                        ], [
                            'workflow_step_type_id' => $workflowStepType->id,
                            'workflow_event_id' => $workflowEventId,
                        ]);
                    }
                }

                $mappedContracts[$workflowStepType->id] = $workflowStepTypeContract::class;
            }

            return $mappedContracts;
        });
    }
}
