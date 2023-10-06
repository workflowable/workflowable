<?php

namespace Workflowable\Workflowable\Commands;

use CodeStencil\Stencil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

class MakeWorkflowConditionTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:workflow-condition-type {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new workflow condition type class.';

    public function handle(): int
    {
        $abstractBaseName = Str::of(AbstractWorkflowConditionType::class)->classBasename();

        Stencil::make()
            ->php()
            ->strictTypes()
            ->use(AbstractWorkflowConditionType::class)
            ->use(WorkflowCondition::class)
            ->use(WorkflowProcess::class)
            ->namespace('App\\Workflowable\\WorkflowConditionTypes')
            ->curlyStatement('class {name} extends '.$abstractBaseName, function (Stencil $stencil) {
            })->save(app_path('Workflowable/WorkflowConditionTypes/'.$this->argument('name').'.php'));

        return self::SUCCESS;
    }
}
