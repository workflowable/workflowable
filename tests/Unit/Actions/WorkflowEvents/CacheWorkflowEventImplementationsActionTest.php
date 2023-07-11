<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowEvents;

use Illuminate\Support\Facades\Cache;
use Workflowable\Workflowable\Actions\WorkflowEvents\CacheWorkflowEventImplementationsAction;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class CacheWorkflowEventImplementationsActionTest extends TestCase
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

        $cache = new CacheWorkflowEventImplementationsAction();
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
