<?php

namespace Workflowable\Workflowable\Commands;

use CodeStencil\Stencil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class MakeWorkflowActivityTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:workflow-activity-type {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new workflow activity type class.';

    public function handle(): int
    {
        $abstractBaseName = Str::of(AbstractWorkflowActivityType::class)->classBasename();

        $name = $this->argument('name');

        Stencil::make()
            ->php()
            ->strictTypes()
            ->use(AbstractWorkflowActivityType::class)
            ->use(WorkflowActivity::class)
            ->use(WorkflowProcess::class)
            ->use(FormManager::class)
            ->namespace('App\\Workflowable\\WorkflowActivityTypes')
            ->curlyStatement("class $name extends ".$abstractBaseName, function (Stencil $stencil) {
                $stencil->indent()
                    ->curlyStatement('public function makeForm(): FormManager', function (Stencil $stencil) {
                        $stencil->indent()
                            ->line('return FormManager::make([]);');
                    })
                    ->newLine()
                    ->curlyStatement('public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool', function (Stencil $stencil) {
                        $stencil->indent()->line('// TODO: Implement handle() method.');
                    });
            })->save(app_path('Workflowable/WorkflowActivityTypes/'.$name.'.php'));

        return self::SUCCESS;
    }
}
