<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\WorkflowStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('friendly_name')->unique();
            $table->timestamps();
        });

        $defaultStatuses = [
            [
                'id' => WorkflowStatus::DRAFT,
                'friendly_name' => 'Draft',
            ],
            [
                'id' => WorkflowStatus::ACTIVE,
                'friendly_name' => 'Active',
            ],
            [
                'id' => WorkflowStatus::INACTIVE,
                'friendly_name' => 'Inactive',
            ],
            [
                'id' => WorkflowStatus::ARCHIVED,
                'friendly_name' => 'Archived',
            ],
        ];

        foreach ($defaultStatuses as $status) {
            WorkflowStatus::query()->forceCreate($status);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_statuses');
    }
};
