<?php

namespace Workflowable\Workflow\Actions\WorkflowEvents;

use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Models\WorkflowEvent;

class CacheWorkflowEventImplementationsAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        $this->shouldBustCache = true;

        return $this;
    }

    public function handle(): array
    {
        $key = config('workflow-engine.cache_keys.workflow_events');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflow-engine.workflow_events') ?? [] as $workflowEventContract) {
                /** @var WorkflowEventContract $workflowEventContract */
                $workflowEventContract = app($workflowEventContract);

                /** @var WorkflowEvent $workflowEvent */
                $workflowEvent = WorkflowEvent::query()
                    ->firstOrCreate([
                        'alias' => $workflowEventContract->getAlias(),
                    ], [
                        'name' => $workflowEventContract->getName(),
                        'alias' => $workflowEventContract->getAlias(),
                    ]);

                $mappedContracts[$workflowEvent->id] = $workflowEventContract::class;
            }

            return $mappedContracts;
        });
    }
}
