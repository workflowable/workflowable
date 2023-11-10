<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;

class ProcessScheduledWorkflowSwapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflowable:process-scheduled-workflow-swaps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Looks for scheduled workflow swaps and ensures that they get run.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        return self::SUCCESS;
    }
}
