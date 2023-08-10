<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
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
                'id' => WorkflowProcessStatusEnum::CREATED,
                'name' => 'Created',
            ],
            [
                'id' => WorkflowProcessStatusEnum::PENDING,
                'name' => 'Pending',
            ],
            [
                'id' => WorkflowProcessStatusEnum::DISPATCHED,
                'name' => 'Dispatched',
            ],
            [
                'id' => WorkflowProcessStatusEnum::RUNNING,
                'name' => 'Running',
            ],
            [
                'id' => WorkflowProcessStatusEnum::PAUSED,
                'name' => 'Paused',
            ],
            [
                'id' => WorkflowProcessStatusEnum::FAILED,
                'name' => 'Failed',
            ],
            [
                'id' => WorkflowProcessStatusEnum::COMPLETED,
                'name' => 'Completed',
            ],
            [
                'id' => WorkflowProcessStatusEnum::CANCELLED,
                'name' => 'Cancelled',
            ],
        ]);
    }
}
