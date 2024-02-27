<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Enums\WorkflowProcessActivityLogStatusEnum;
use Workflowable\Workflowable\Models\WorkflowProcessActivityLogStatus;

class WorkflowActivityAttemptStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkflowProcessActivityLogStatus::query()->insert([
            [
                'id' => WorkflowProcessActivityLogStatusEnum::IN_PROGRESS,
                'name' => 'In Progress',
            ],
            [
                'id' => WorkflowProcessActivityLogStatusEnum::SUCCESS,
                'name' => 'Success',
            ],
            [
                'id' => WorkflowProcessActivityLogStatusEnum::FAILURE,
                'name' => 'Failure',
            ],
        ]);
    }
}
