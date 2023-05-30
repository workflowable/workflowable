<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowConditionTypes;

use Illuminate\Support\Facades\Cache;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\CacheWorkflowConditionTypeImplementationsAction;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class CacheWorkflowConditionTypeImplementationsActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_condition_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflow-engine.cache_keys.workflow_condition_types'), \Closure::class)
            ->andReturn([
                WorkflowConditionTypeFake::class,
            ]);

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->handle();
    }

    public function test_it_can_bust_the_cache_when_needed()
    {
        Cache::shouldReceive('forget')
            ->once();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflow-engine.cache_keys.workflow_condition_types'), \Closure::class)
            ->andReturn([
                WorkflowConditionTypeFake::class,
            ]);

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();
    }

    public function test_that_if_workflow_event_dependency_doesnt_exist_we_will_skip_the_workflow_condition_type()
    {
        $this->partialMock(WorkflowConditionTypeFake::class, function ($mock) {
            $mock->shouldReceive('getWorkflowEventAlias')
                ->andReturn('fake-event');
        });

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseEmpty(WorkflowConditionType::class);
    }

    public function test_it_can_create_workflow_condition_type_if_not_exists()
    {
        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowConditionTypeFake = new WorkflowConditionTypeFake();

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'alias' => $workflowConditionTypeFake->getAlias(),
            'name' => $workflowConditionTypeFake->getName(),
        ]);
    }

    public function test_it_does_not_create_workflow_step_type_if_already_exists()
    {
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowConditionTypeFake = new WorkflowConditionTypeFake();

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'alias' => $workflowConditionTypeFake->getAlias(),
            'name' => $workflowConditionTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowConditionType::class, 1);
    }

    public function test_if_event_constrained_we_create_pivot_between_condition_type_and_event()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowConditionTypeFake = new WorkflowConditionTypeEventConstrainedFake();

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'alias' => $workflowConditionTypeFake->getAlias(),
            'name' => $workflowConditionTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowConditionType::class, 1);

        $this->assertDatabaseCount(WorkflowConditionTypeWorkflowEvent::class, 1);

        $this->assertDatabaseHas(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_condition_type_id' => WorkflowConditionType::query()->where('alias', $workflowConditionTypeFake->getAlias())->first()->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }

    public function test_we_do_not_double_up_on_pivot_table_to_workflow_event()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowConditionTypeFake = new WorkflowConditionTypeEventConstrainedFake();

        $workflowConditionType = WorkflowConditionType::factory()->withContract($workflowConditionTypeFake)->create();

        $cache = new CacheWorkflowConditionTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'alias' => $workflowConditionTypeFake->getAlias(),
            'name' => $workflowConditionTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowConditionType::class, 1);

        $this->assertDatabaseCount(WorkflowConditionTypeWorkflowEvent::class, 1);

        $this->assertDatabaseHas(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_condition_type_id' => WorkflowConditionType::query()->where('alias', $workflowConditionTypeFake->getAlias())->first()->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }
}
