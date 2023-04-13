<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Abstracts\AbstractData;
use Workflowable\Workflow\Managers\WorkflowConditionTypeTypeManager;

class WorkflowConditionData extends AbstractData
{
    protected int $id;

    protected int $workflowTransitionId;

    protected int $workflowConditionTypeId;

    protected int $ordinal;

    protected array $parameters;

    public function fromArray(array $data): self
    {
        $manager = new WorkflowConditionTypeTypeManager();

        $isValid = $manager->isValid($data['workflow_action_type_id'], $data['parameters'] ?? []);
        if (! $isValid) {
            throw new \Exception('Invalid core condition parameters');
        }

        $this->id = $data['id'] ?? null;
        $this->workflowTransitionId = $data['workflow_transition_id'] ?? null;
        $this->workflowConditionTypeId = $data['workflow_condition_type_id'] ?? null;
        $this->ordinal = $data['ordinal'] ?? null;
        $this->parameters = $data['parameters'] ?? null;

        return $this;
    }
}
