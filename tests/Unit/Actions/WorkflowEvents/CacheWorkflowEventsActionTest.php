<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowEvents;

use Illuminate\Support\Facades\Cache;
use Workflowable\Workflow\Actions\WorkflowEvents\CacheWorkflowEventsAction;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class CacheWorkflowEventsActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_condition_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_events'), \Closure::class)
            ->andReturn([
                WorkflowEventFake::class,
            ]);

        $cache = new CacheWorkflowEventsAction();
        $cache->handle();
    }

    public function test_it_can_bust_the_cache_when_needed()
    {
        Cache::shouldReceive('forget')
            ->once();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_events'), \Closure::class)
            ->andReturn([
                WorkflowEventFake::class,
            ]);

        $cache = new CacheWorkflowEventsAction();
        $cache->shouldBustCache()->handle();
    }

    public function test_it_can_create_workflow_condition_type_if_not_exists()
    {
        $cache = new CacheWorkflowEventsAction();
        $cache->shouldBustCache()->handle();

        $workflowEventFake = new WorkflowEventFake();

        $this->assertDatabaseHas(WorkflowEvent::class, [
            'alias' => $workflowEventFake->getAlias(),
            'friendly_name' => $workflowEventFake->getFriendlyName(),
        ]);
    }

    public function test_it_does_not_create_workflow_step_type_if_already_exists()
    {
        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $cache = new CacheWorkflowEventsAction();
        $cache->shouldBustCache()->handle();

        $workflowEventFake = new WorkflowEventFake();

        $this->assertDatabaseHas(WorkflowEvent::class, [
            'alias' => $workflowEventFake->getAlias(),
            'friendly_name' => $workflowEventFake->getFriendlyName(),
        ]);

        $this->assertDatabaseCount(WorkflowEvent::class, 1);
    }
}
