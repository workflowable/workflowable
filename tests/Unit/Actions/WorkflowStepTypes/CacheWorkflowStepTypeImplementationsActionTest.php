<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowStepTypes;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Workflowable\Workflowable\Actions\WorkflowStepTypes\CacheWorkflowStepTypeImplementationsAction;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEventWorkflowStepType;
use Workflowable\Workflowable\Models\WorkflowStepType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class CacheWorkflowStepTypeImplementationsActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_step_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_step_types'), \Closure::class)
            ->andReturn([
                WorkflowStepTypeFake::class,
            ]);

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->handle();
    }

    public function test_it_can_bust_the_cache_when_needed()
    {
        Cache::shouldReceive('forget')
            ->once();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_step_types'), \Closure::class)
            ->andReturn([
                WorkflowStepTypeFake::class,
            ]);

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();
    }

    public function test_it_can_create_workflow_step_type_if_not_exists()
    {
        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowStepTypeFake = new WorkflowStepTypeFake();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'name' => $workflowStepTypeFake->getName(),
        ]);
    }

    public function test_it_does_not_create_workflow_step_type_if_already_exists()
    {
        WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create();

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowStepTypeFake = new WorkflowStepTypeFake();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'name' => $workflowStepTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowStepType::class, 1);
    }

    public function test_if_event_constrained_we_create_pivot_between_condition_type_and_event()
    {
        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowStepTypeFake = new WorkflowStepTypeEventConstrainedFake();

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'name' => $workflowStepTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowStepType::class, 1);

        $this->assertDatabaseCount(WorkflowEventWorkflowStepType::class, 1);

        $this->assertDatabaseHas(WorkflowEventWorkflowStepType::class, [
            'workflow_step_type_id' => WorkflowStepType::query()->where('alias', $workflowStepTypeFake->getAlias())->firstOrFail()->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }

    public function test_we_do_not_double_up_on_pivot_table_to_workflow_event()
    {
        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowStepTypeFake = new WorkflowStepTypeEventConstrainedFake();

        $workflowStepType = WorkflowStepType::factory()->withContract($workflowStepTypeFake)->create();

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'name' => $workflowStepTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowStepType::class, 1);

        $this->assertDatabaseCount(WorkflowEventWorkflowStepType::class, 1);

        $this->assertDatabaseHas(WorkflowEventWorkflowStepType::class, [
            'workflow_step_type_id' => $workflowStepType->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }
}
