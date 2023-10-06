<?php

namespace Workflowable\Workflowable\Commands;

use CodeStencil\Stencil;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
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
        $abstractBaseName = Str::of(AbstractWorkflowConditionType::class)->classBasename();

        Stencil::make()
            ->php()
            ->strictTypes()
            ->use(AbstractWorkflowEvent::class)
            ->namespace('App\\Workflowable\\WorkflowEvents')
            ->curlyStatement('class {name} extends '.$abstractBaseName, function (Stencil $stencil) {
                $stencil->curlyStatement('public function getRules(): array', function (Stencil $stencil) {
                    $stencil->line('return[];');
                });
            })
            ->save(app_path('Workflowable/WorkflowEvents/'.$this->argument('name').'.php'));

        return self::SUCCESS;
    }
}
