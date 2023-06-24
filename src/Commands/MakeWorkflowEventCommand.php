<?php

namespace Workflowable\WorkflowEngine\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeWorkflowEventCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:workflow-event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new workflow event class.';

    protected $type = 'class';

    public function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows\\Events';
    }

    protected function getStub()
    {
        return __DIR__.'/../../stubs/make-workflow-event.stub';
    }

    public function replaceClass($stub, $name): string
    {
        parent::replaceClass($stub, $name);

        $stub = str_replace('WorkflowEventClassName', $this->argument('name'), $stub);
        $stub = str_replace('workflow_event_alias', Str::snake($this->argument('name'), '_'), $stub);
        $stub = str_replace('Workflow Event Name', Str::headline($this->argument('name')), $stub);

        return $stub;
    }
}
