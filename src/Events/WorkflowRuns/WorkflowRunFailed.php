<?php

namespace Workflowable\Workflow\Events\WorkflowRuns;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;

class WorkflowRunFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public WorkflowRun $workflowRun)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(config('workflowable.broadcast_channel')),
        ];
    }
}
