<?php

namespace Workflowable\Workflowable\Events\WorkflowSwaps;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowSwapDispatched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public WorkflowSwap $workflowSwap)
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
