<?php

namespace Workflowable\Workflowable\Database\seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Models\WorkflowRunStatus;

class WorkflowRunStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkflowRunStatus::query()->insert([
            [
                'id' => WorkflowRunStatus::CREATED,
                'name' => 'Created',
            ],
            [
                'id' => WorkflowRunStatus::PENDING,
                'name' => 'Pending',
            ],
            [
                'id' => WorkflowRunStatus::DISPATCHED,
                'name' => 'Dispatched',
            ],
            [
                'id' => WorkflowRunStatus::RUNNING,
                'name' => 'Running',
            ],
            [
                'id' => WorkflowRunStatus::PAUSED,
                'name' => 'Paused',
            ],
            [
                'id' => WorkflowRunStatus::FAILED,
                'name' => 'Failed',
            ],
            [
                'id' => WorkflowRunStatus::COMPLETED,
                'name' => 'Completed',
            ],
            [
                'id' => WorkflowRunStatus::CANCELLED,
                'name' => 'Cancelled',
            ],
        ]);
    }
}
