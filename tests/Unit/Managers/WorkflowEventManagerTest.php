<?php

namespace Workflowable\Workflow\Tests\Unit\Managers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflow\Tests\TestCase;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Managers\WorkflowEventManager;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;

class WorkflowEventManagerTest extends TestCase
{
    use DatabaseTransactions;

    protected WorkflowEventContract $dummyWorkflowEvent;

    public function setUp(): void
    {
        parent::setUp();

        $this->dummyWorkflowEvent = new WorkflowEventFake('stuff');
    }

    public function test_that_we_can_register_a_workflow_event(): void
    {
        $manager = new WorkflowEventManager();

        $result = $manager->register($this->dummyWorkflowEvent);

        $this->assertInstanceOf(WorkflowEventManagerContract::class, $result);
        $this->assertTrue($manager->isRegistered('workflow_event_fake'));
        $this->assertEquals(['workflow_event_fake' => $this->dummyWorkflowEvent], $manager->getImplementations());
        $this->assertEquals($this->dummyWorkflowEvent, $manager->getImplementationByAlias('workflow_event_fake'));
        $this->assertEquals(['test' => 'required|string|min:4'], $manager->getRules('workflow_event_fake'));

        $data = [
            'test' => 'test',
        ];

        $this->assertTrue($manager->isValid('workflow_event_fake', $data));
    }

    public function test_cannot_get_unknown_event()
    {
        $manager = new WorkflowEventManager();

        $this->expectException(WorkflowEventException::class);
        $this->expectExceptionMessage(WorkflowEventException::workflowEventNotRegistered('unknown_event')->getMessage());

        $manager->getImplementationByAlias('unknown_event');
    }

    /**
     * Test that invalid data fails validation
     *
     * @return void
     */
    public function test_invalid_data_fails_validation()
    {
        $event = new class implements WorkflowEventContract
        {
            public function getAlias(): string
            {
                return 'test_event';
            }

            public function getRules(): array
            {
                return [
                    'field1' => 'required',
                    'field2' => 'numeric',
                ];
            }

            public function getFriendlyName(): string
            {
                return 'Test Event';
            }
        };

        $manager = new WorkflowEventManager();
        $manager->register($event);

        $data = [
            'field1' => 'test',
            'field2' => 'abc', // This should be numeric, but it's a string
        ];

        $this->assertFalse($manager->isValid('test_event', $data));
    }
}
