<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowEvents;

use Illuminate\Support\Facades\Cache;
use Workflowable\WorkflowEngine\Actions\WorkflowEvents\CacheWorkflowEventImplementationsAction;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class CacheWorkflowEventImplementationsActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflow-engine.workflow_events', [
            WorkflowEventFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_condition_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflow-engine.cache_keys.workflow_events'), \Closure::class)
            ->andReturn([
                WorkflowEventFake::class,
            ]);

        $cache = new CacheWorkflowEventImplementationsAction();
        $cache->handle();
    }

    public function test_it_can_bust_the_cache_when_needed()
    {
        Cache::shouldReceive('forget')
            ->once();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflow-engine.cache_keys.workflow_events'), \Closure::class)
            ->andReturn([
                WorkflowEventFake::class,
            ]);

        $cache = new CacheWorkflowEventImplementationsAction();
        $cache->shouldBustCache()->handle();
    }

    public function test_it_can_create_workflow_condition_type_if_not_exists()
    {
        $cache = new CacheWorkflowEventImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowEventFake = new WorkflowEventFake();

        $this->assertDatabaseHas(WorkflowEvent::class, [
            'alias' => $workflowEventFake->getAlias(),
            'name' => $workflowEventFake->getName(),
        ]);
    }

    public function test_it_does_not_create_workflow_step_type_if_already_exists()
    {
        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $cache = new CacheWorkflowEventImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowEventFake = new WorkflowEventFake();

        $this->assertDatabaseHas(WorkflowEvent::class, [
            'alias' => $workflowEventFake->getAlias(),
            'name' => $workflowEventFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowEvent::class, 1);
    }
}
