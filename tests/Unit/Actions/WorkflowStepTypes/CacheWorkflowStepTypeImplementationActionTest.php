<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowStepTypes;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Workflowable\Workflow\Actions\WorkflowStepTypes\CacheWorkflowStepTypeImplementationsAction;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CacheWorkflowStepTypeImplementationActionTest extends TestCase
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
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_it_can_create_workflow_step_type_if_not_exists()
    {
        $cache = new CacheWorkflowStepTypeImplementationsAction();
        $cache->shouldBustCache()->handle();

        $workflowStepTypeFake = new WorkflowStepTypeFake();

        $this->assertDatabaseHas(WorkflowStepType::class, [
            'alias' => $workflowStepTypeFake->getAlias(),
            'friendly_name' => $workflowStepTypeFake->getFriendlyName(),
            'workflow_event_id' => null,
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
            'workflow_event_id' => null,
        ]);

        $this->assertDatabaseCount(WorkflowStepType::class, 1);
    }
}
