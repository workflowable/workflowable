<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWorkflowActivityTypeCommand extends GeneratorCommand
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

    protected $type = 'class';

    public function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows\\ActivityTypes';
    }

    protected function getStub()
    {
        return __DIR__.'/../../stubs/make-workflow-activity-type.stub';
    }

    public function replaceClass($stub, $name): string
    {
        parent::replaceClass($stub, $name);

        $stub = str_replace('WorkflowActivityTypeClassName', $this->argument('name'), $stub);
        $stub = str_replace('workflow_activity_type_alias', Str::snake($this->argument('name'), '_'), $stub);

        return str_replace('Workflow Activity Type Name', Str::headline($this->argument('name')), $stub);
    }
}
