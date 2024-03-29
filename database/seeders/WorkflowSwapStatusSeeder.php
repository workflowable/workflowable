<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Models\WorkflowSwapStatus;

class WorkflowSwapStatusSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowSwapStatus::query()->insert([
            [
                'id' => WorkflowSwapStatusEnum::Draft,
                'name' => 'Draft',
            ],
            [
                'id' => WorkflowSwapStatusEnum::Scheduled,
                'name' => 'Scheduled',
            ],
            [
                'id' => WorkflowSwapStatusEnum::Dispatched,
                'name' => 'Dispatched',
            ],
            [
                'id' => WorkflowSwapStatusEnum::Processing,
                'name' => 'Processing',
            ],
            [
                'id' => WorkflowSwapStatusEnum::Completed,
                'name' => 'Completed',
            ],
        ]);
    }
}
