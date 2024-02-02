<?php

namespace Workflowable\Workflowable\Commands;

use CodeStencil\Stencil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;

class MakeWorkflowEventCommand extends Command
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

    public function handle(): int
    {
        $abstractBaseName = Str::of(AbstractWorkflowEvent::class)->classBasename();

        $eventName = $this->argument('name');

        Stencil::make()
            ->php()
            ->strictTypes()
            ->use(AbstractWorkflowEvent::class)
            ->use(FormManager::class)
            ->namespace('App\\Workflowable\\WorkflowEvents')
            ->curlyStatement("class $eventName extends ".$abstractBaseName, function (Stencil $stencil) {
                $stencil->curlyStatement('public function makeForm(): FormManager', function (Stencil $stencil) {
                    $stencil->indent()->line('return FormManager::make([]);');
                });
            })
            ->save(app_path('Workflowable/WorkflowEvents/'.$eventName.'.php'));

        return self::SUCCESS;
    }
}
