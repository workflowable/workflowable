<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Abstracts\AbstractData;

class WorkflowTransitionData extends AbstractData
{
    protected int $id;

    /**
     * @var string The name of the transition
     */
    protected string $name;

    /**
     * @var int The ID of the core action that we will transition from
     */
    protected int $from_workflow_action_id;

    /**
     * @var int The ID of the core action that we will transition to
     */
    protected int $to_workflow_action_id;

    /**
     * @var int The ordinal of the transition.  We will process the transitions in order of this value from
     *                   lowest to highest
     */
    protected int $ordinal;

    public function fromArray(array $data): self
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['friendly_name'] ?? null;
        $this->from_workflow_action_id = $data['from_workflow_action_id'] ?? null;
        $this->to_workflow_action_id = $data['to_workflow_action_id'] ?? null;
        $this->ordinal = $data['ordinal'] ?? null;

        return $this;
    }
}
