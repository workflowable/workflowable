<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\GeneratorCommand;

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

        return str_replace('WorkflowActivityTypeClassName', $this->argument('name'), $stub);
    }
}
