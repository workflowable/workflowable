<?php

namespace Workflowable\WorkflowEngine\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWorkflowStepTypeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:workflow-step-type {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new workflow step type class.';

    protected $type = 'class';

    public function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows\\StepTypes';
    }

    protected function getStub()
    {
        return __DIR__.'/../../stubs/make-workflow-step-type.stub';
    }

    public function replaceClass($stub, $name): string
    {
        parent::replaceClass($stub, $name);

        $stub = str_replace('WorkflowStepTypeClassName', $this->argument('name'), $stub);
        $stub = str_replace('workflow_step_type_alias', Str::snake($this->argument('name'), '_'), $stub);

        return str_replace('Workflow Step Type Name', Str::headline($this->argument('name')), $stub);
    }
}
