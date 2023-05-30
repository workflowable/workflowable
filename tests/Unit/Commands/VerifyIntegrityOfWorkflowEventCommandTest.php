<?php

namespace Workflowable\Workflow\Tests\Unit\Commands;

use Workflowable\Workflow\Tests\TestCase;

class VerifyIntegrityOfWorkflowEventCommandTest extends TestCase
{
    public function test_that_it_logs_an_error_when_workflow_condition_type_requires_keys_not_in_workflow_event()
    {
        // https://laravel.com/docs/10.x/console-tests
        $this->markTestSkipped('Not implemented yet.');
    }

    public function test_that_it_logs_an_error_when_workflow_step_type_requires_keys_not_in_workflow_event()
    {
        $this->markTestSkipped('Not implemented yet.');
    }

    public function test_that_it_logs_no_error_when_workflow_condition_type_requires_keys_in_workflow_event()
    {
        $this->markTestSkipped('Not implemented yet.');
    }

    public function test_that_it_logs_no_error_when_workflow_step_type_requires_keys_in_workflow_event()
    {
        $this->markTestSkipped('Not implemented yet.');
    }
}
