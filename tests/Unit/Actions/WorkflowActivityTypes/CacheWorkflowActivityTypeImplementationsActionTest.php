<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivityTypes;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\CacheWorkflowActivityTypeImplementationsAction;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class CacheWorkflowActivityTypeImplementationsActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_activity_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_activity_types'), \Closure::class)
            ->andReturn([
                WorkflowActivityTypeFake::class,
            ]);

        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->handle();
    }

    public function test_it_can_bust_the_cache_when_needed()
    {
        Cache::shouldReceive('forget')
            ->once();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_activity_types'), \Closure::class)
            ->andReturn([
                WorkflowActivityTypeFake::class,
            ]);

        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->shouldBustCache()->handle();
    }

    public function test_it_can_create_workflow_activity_type_if_not_exists()
    {
        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowActivityTypeFake = new WorkflowActivityTypeFake();

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'alias' => $workflowActivityTypeFake->getAlias(),
            'name' => $workflowActivityTypeFake->getName(),
        ]);
    }

    public function test_it_does_not_create_workflow_activity_type_if_already_exists()
    {
        WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create();

        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowActivityTypeFake = new WorkflowActivityTypeFake();

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'alias' => $workflowActivityTypeFake->getAlias(),
            'name' => $workflowActivityTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowActivityType::class, 1);
    }

    public function test_if_event_constrained_we_create_pivot_between_condition_type_and_event()
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowActivityTypeFake = new WorkflowActivityTypeEventConstrainedFake();

        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'alias' => $workflowActivityTypeFake->getAlias(),
            'name' => $workflowActivityTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowActivityType::class, 1);

        $this->assertDatabaseCount(WorkflowActivityTypeWorkflowEvent::class, 1);

        $this->assertDatabaseHas(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_activity_type_id' => WorkflowActivityType::query()->where('alias', $workflowActivityTypeFake->getAlias())->firstOrFail()->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }

    public function test_we_do_not_double_up_on_pivot_table_to_workflow_event()
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeEventConstrainedFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowActivityTypeFake = new WorkflowActivityTypeEventConstrainedFake();

        $workflowActivityType = WorkflowActivityType::factory()->withContract($workflowActivityTypeFake)->create();

        $cache = new CacheWorkflowActivityTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'alias' => $workflowActivityTypeFake->getAlias(),
            'name' => $workflowActivityTypeFake->getName(),
        ]);

        $this->assertDatabaseCount(WorkflowActivityType::class, 1);

        $this->assertDatabaseCount(WorkflowActivityTypeWorkflowEvent::class, 1);

        $this->assertDatabaseHas(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_activity_type_id' => $workflowActivityType->id,
            'workflow_event_id' => $workflowEvent->id,
        ]);
    }
}
