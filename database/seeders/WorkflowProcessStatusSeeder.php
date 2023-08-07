<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;

class WorkflowProcessStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkflowProcessStatus::query()->insert([
            [
                'id' => WorkflowProcessStatus::CREATED,
                'name' => 'Created',
            ],
            [
                'id' => WorkflowProcessStatus::PENDING,
                'name' => 'Pending',
            ],
            [
                'id' => WorkflowProcessStatus::DISPATCHED,
                'name' => 'Dispatched',
            ],
            [
                'id' => WorkflowProcessStatus::RUNNING,
                'name' => 'Running',
            ],
            [
                'id' => WorkflowProcessStatus::PAUSED,
                'name' => 'Paused',
            ],
            [
                'id' => WorkflowProcessStatus::FAILED,
                'name' => 'Failed',
            ],
            [
                'id' => WorkflowProcessStatus::COMPLETED,
                'name' => 'Completed',
            ],
            [
                'id' => WorkflowProcessStatus::CANCELLED,
                'name' => 'Cancelled',
            ],
        ]);
    }
}
