<?php

namespace Workflowable\Workflowable\Commands;

use CodeStencil\Stencil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Form\Form;
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

        $name = $this->argument('name');

        Stencil::make()
            ->php()
            ->strictTypes()
            ->use(AbstractWorkflowConditionType::class)
            ->use(WorkflowCondition::class)
            ->use(WorkflowProcess::class)
            ->use(Form::class)
            ->namespace('App\\Workflowable\\WorkflowConditionTypes')
            ->curlyStatement("class $name extends ".$abstractBaseName, function (Stencil $stencil) {
                $stencil->curlyStatement('public function makeForm(): Form', function (Stencil $stencil) {
                    $stencil->line('return Form::make([]);');
                })
                    ->newLine()
                    ->curlyStatement('public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool', function (Stencil $stencil) {
                        $stencil->indent()->line('// TODO: Implement handle() method.');
                    });
            })->save(app_path('Workflowable/WorkflowConditionTypes/'.$name.'.php'));

        return self::SUCCESS;
    }
}
