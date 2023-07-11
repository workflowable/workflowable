<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_run_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        WorkflowRunStatus::query()->insert([
            [
                'id' => WorkflowRunStatus::CREATED,
                'name' => 'Created',
            ],
            [
                'id' => WorkflowRunStatus::PENDING,
                'name' => 'Pending',
            ],
            [
                'id' => WorkflowRunStatus::DISPATCHED,
                'name' => 'Dispatched',
            ],
            [
                'id' => WorkflowRunStatus::RUNNING,
                'name' => 'Running',
            ],
            [
                'id' => WorkflowRunStatus::PAUSED,
                'name' => 'Paused',
            ],
            [
                'id' => WorkflowRunStatus::FAILED,
                'name' => 'Failed',
            ],
            [
                'id' => WorkflowRunStatus::COMPLETED,
                'name' => 'Completed',
            ],
            [
                'id' => WorkflowRunStatus::CANCELLED,
                'name' => 'Cancelled',
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
