<?php

namespace Workflowable\Workflowable\Database\seeders;

use Illuminate\Database\Seeder;
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
                'id' => WorkflowStatus::DRAFT,
                'name' => 'Draft',
            ],
            [
                'id' => WorkflowStatus::ACTIVE,
                'name' => 'Active',
            ],
            [
                'id' => WorkflowStatus::DEACTIVATED,
                'name' => 'Deactivated',
            ],
            [
                'id' => WorkflowStatus::ARCHIVED,
                'name' => 'Archived',
            ],
        ]);
    }
}
