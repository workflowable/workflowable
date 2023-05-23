<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowStepTypes;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\CacheWorkflowConditionTypeImplementationsAction;
use Workflowable\Workflow\Actions\WorkflowStepTypes\CacheWorkflowStepTypeImplementationsAction;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEventWorkflowStepType;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeEventConstrainedFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

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

    public function test_that_if_workflow_event_dependency_doesnt_exist_we_will_skip_the_workflow_step_type()
    {
        $this->partialMock(WorkflowStepTypeFake::class, function ($mock) {
            $mock->shouldReceive('getWorkflowEventAlias')
                ->andReturn('fake-event');
        });

        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseEmpty(WorkflowStepType::class);
    }

    public function test_it_can_create_workflow_step_type_if_not_exists()
    {
        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowStepTypeFake = new WorkflowStepTypeFake();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'friendly_name' => $workflowStepTypeFake->getFriendlyName(),
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
            'friendly_name' => $workflowStepTypeFake->getFriendlyName(),
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
            'friendly_name' => $workflowStepTypeFake->getFriendlyName(),
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
            'friendly_name' => $workflowStepTypeFake->getFriendlyName(),
        ]);

        $this->assertDatabaseCount(WorkflowStepType::class, 1);

        $this->assertDatabaseCount(WorkflowEventWorkflowStepType::class, 1);

        $this->assertDatabaseHas(WorkflowEventWorkflowStepType::class, [
            'workflow_step_type_id' => $workflowStepType->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }
}
