<?php

namespace Workflowable\Workflow\Tests\Unit\Jobs;

use Workflowable\Workflow\Tests\TestCase;

class WorkflowRunnerJobTest extends TestCase
{
    public function test_that_we_can_process_a_workflow_run(): void
    {
        $this->markTestIncomplete();
    }

    public function test_that_if_more_steps_are_available_we_will_process_them(): void
    {
        $this->markTestIncomplete();
    }

    public function test_that_if_more_steps_exist_but_are_not_available_we_will_not_process_them(): void
    {
        $this->markTestIncomplete();
    }

    public function test_that_if_a_step_fails_we_will_not_process_any_more_steps(): void
    {
        $this->markTestIncomplete();
    }

    public function test_that_if_there_are_no_more_steps_we_will_mark_the_workflow_run_as_complete(): void
    {
        $this->markTestIncomplete();
    }
}
