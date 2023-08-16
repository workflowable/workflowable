<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Enums\WorkflowActivityAttemptStatusEnum;
use Workflowable\Workflowable\Models\WorkflowActivityAttemptStatus;

class WorkflowActivityAttemptStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkflowActivityAttemptStatus::query()->insert([
            [
                'id' => WorkflowActivityAttemptStatusEnum::SUCCESS,
                'name' => 'Success',
            ],
            [
                'id' => WorkflowActivityAttemptStatusEnum::FAILURE,
                'name' => 'Failure',
            ],
        ]);
    }
}
