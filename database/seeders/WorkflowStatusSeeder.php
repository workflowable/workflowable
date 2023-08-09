<?php

namespace Workflowable\Workflowable\Database\Seeders;

use Illuminate\Database\Seeder;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\WorkflowStatus;

class WorkflowStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkflowStatus::query()->insert([
            [
                'id' => WorkflowStatusEnum::DRAFT,
                'name' => 'Draft',
            ],
            [
                'id' => WorkflowStatusEnum::ACTIVE,
                'name' => 'Active',
            ],
            [
                'id' => WorkflowStatusEnum::DEACTIVATED,
                'name' => 'Deactivated',
            ],
            [
                'id' => WorkflowStatusEnum::ARCHIVED,
                'name' => 'Archived',
            ],
        ]);
    }
}
