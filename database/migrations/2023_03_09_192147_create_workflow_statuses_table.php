<?php

namespace Workflowable\WorkflowEngine\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $defaultStatuses = [
            [
                'id' => WorkflowStatus::DRAFT,
                'name' => 'Draft',
            ],
            [
                'id' => WorkflowStatus::ACTIVE,
                'name' => 'Active',
            ],
            [
                'id' => WorkflowStatus::INACTIVE,
                'name' => 'Inactive',
            ],
            [
                'id' => WorkflowStatus::ARCHIVED,
                'name' => 'Archived',
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
