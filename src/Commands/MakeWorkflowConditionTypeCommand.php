<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\GeneratorCommand;

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
    protected $description = 'Creates a new workflow condition type class.';

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

        return str_replace('WorkflowConditionTypeClassName', $this->argument('name'), $stub);
    }
}
