<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Abstracts\AbstractData;
use Workflowable\Workflow\Managers\WorkflowStepTypeTypeManager;

class WorkflowActionData extends AbstractData
{
    protected int $id;

    protected int $workflowId;

    protected int $workflowActionTypeId;

    protected string $name;

    protected string $description;

    protected array $parameters;

    /**
     * @throws \Exception
     */
    public function fromArray(array $data): AbstractData
    {
        $manager = new WorkflowStepTypeTypeManager();

        $isValid = $manager->isValid($data['workflow_action_type_id'], $data['parameters'] ?? []);
        if (! $isValid) {
            throw new \Exception('Invalid core action parameters');
        }

        $this->id = $data['id'] ?? null;
        $this->workflowId = $data['workflow_id'] ?? null;
        $this->workflowActionTypeId = $data['workflow_action_type_id'] ?? null;
        $this->name = $data['friendly_name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->parameters = $data['parameters'] ?? null;

        return $this;
    }
}
