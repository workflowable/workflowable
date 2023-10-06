<?php

namespace Workflowable\Workflowable\Actions\WorkflowEvents;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Models\WorkflowEvent;

class CacheWorkflowEventImplementationsAction extends AbstractAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        $this->shouldBustCache = true;

        return $this;
    }

    public function handle(): array
    {
        $key = config('workflowable.cache_keys.workflow_events');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflowable.workflow_events') ?? [] as $workflowEventContract) {
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
