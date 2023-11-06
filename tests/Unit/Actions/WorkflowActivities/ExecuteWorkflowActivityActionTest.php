<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Workflowable\Workflowable\Tests\TestCase;

class ExecuteWorkflowActivityActionTest extends TestCase
{
    public function test_that_upon_starting_the_execution_of_a_workflow_activity_we_will_dispatch_the_started_event()
    {
        $this->markTestIncomplete('Not written yet');
    }

    public function test_that_upon_successfully_completing_execution_we_will_dispatch_the_completed_event()
    {
        $this->markTestIncomplete('Not written yet');
    }

    public function test_that_upon_failing_to_execute_the_activity_we_will_dispatch_the_failed_event()
    {
        $this->markTestIncomplete('Not written yet');
    }
}
