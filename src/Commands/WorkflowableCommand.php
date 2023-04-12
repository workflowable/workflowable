<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;

class WorkflowableCommand extends Command
{
    public $signature = 'core';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
