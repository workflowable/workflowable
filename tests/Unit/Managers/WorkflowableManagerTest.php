<?php

namespace Workflowable\Workflowable\Tests\Unit\Managers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class WorkflowableManagerTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_create_an_output_token_for_our_workflow_process()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $result = $this->manager->createOutputToken($this->workflowProcess, $this->fromWorkflowActivity, 'test', 'test');
        $this->assertInstanceOf(WorkflowProcessToken::class, $result);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $this->workflowProcess->id,
            'workflow_activity_id' => $this->fromWorkflowActivity->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }
}
