<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\WorkflowRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_run_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('friendly_name')->unique();
            $table->timestamps();
        });

        WorkflowRunStatus::query()->insert([
            [
                'id' => WorkflowRunStatus::CREATED,
                'friendly_name' => 'Created',
            ],
            [
                'id' => WorkflowRunStatus::PENDING,
                'friendly_name' => 'Pending',
            ],
            [
                'id' => WorkflowRunStatus::DISPATCHED,
                'friendly_name' => 'Dispatched',
            ],
            [
                'id' => WorkflowRunStatus::RUNNING,
                'friendly_name' => 'Running',
            ],
            [
                'id' => WorkflowRunStatus::PAUSED,
                'friendly_name' => 'Paused',
            ],
            [
                'id' => WorkflowRunStatus::FAILED,
                'friendly_name' => 'Failed',
            ],
            [
                'id' => WorkflowRunStatus::COMPLETED,
                'friendly_name' => 'Completed',
            ],
            [
                'id' => WorkflowRunStatus::CANCELLED,
                'friendly_name' => 'Cancelled',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_run_statuses');
    }
};
