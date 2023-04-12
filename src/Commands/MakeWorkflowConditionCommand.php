<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWorkflowConditionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:workflow-condition {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $type = 'class';

    public function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows\\Conditions';
    }

    protected function getStub()
    {
        return __DIR__.'/../../stubs/make-workflow-condition.stub';
    }

    public function replaceClass($stub, $name): string
    {
        parent::replaceClass($stub, $name);

        $stub = str_replace('WorkflowConditionClassName', $this->argument('name'), $stub);
        $stub = str_replace('workflow_condition_alias', Str::snake($this->argument('name'), '_'), $stub);

        return str_replace('Workflow Condition Friendly Name', Str::headline($this->argument('name'), ' '), $stub);
    }
}
