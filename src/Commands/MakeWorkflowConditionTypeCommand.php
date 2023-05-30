<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWorkflowConditionTypeCommand extends GeneratorCommand
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
    protected $description = 'Command description';

    protected $type = 'class';

    public function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows\\ConditionTypes';
    }

    protected function getStub()
    {
        return __DIR__.'/../../stubs/make-workflow-condition-type.stub';
    }

    public function replaceClass($stub, $name): string
    {
        parent::replaceClass($stub, $name);

        $stub = str_replace('WorkflowConditionTyeClassName', $this->argument('name'), $stub);
        $stub = str_replace('workflow_condition_type_alias', Str::snake($this->argument('name'), '_'), $stub);

        return str_replace('Workflow Condition Type Name', Str::headline($this->argument('name')), $stub);
    }
}
