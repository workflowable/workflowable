<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowConditionTypes;

use Illuminate\Support\Facades\Cache;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\CacheWorkflowConditionTypeImplementationsAction;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CacheWorkflowConditionTypeImplementationsActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);
    }

    public function test_it_can_cache_workflow_condition_types()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with(config('workflowable.cache_keys.workflow_condition_types'), \Closure::class)
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
            ->with(config('workflowable.cache_keys.workflow_condition_types'), \Closure::class)
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
            'friendly_name' => $workflowConditionTypeFake->getFriendlyName(),
            'workflow_event_id' => null,
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
            'friendly_name' => $workflowConditionTypeFake->getFriendlyName(),
            'workflow_event_id' => null,
        ]);

        $this->assertDatabaseCount(WorkflowConditionType::class, 1);
    }
}
