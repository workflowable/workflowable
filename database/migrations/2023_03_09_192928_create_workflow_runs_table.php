<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Workflow::class, 'workflow_id')->constrained('workflows');
            $table->foreignIdFor(WorkflowRunStatus::class, 'workflow_run_status_id')
                ->constrained('workflow_run_statuses')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivity::class, 'last_workflow_activity_id')
                ->nullable()
                ->constrained('workflow_activities')
                ->cascadeOnDelete();
            $table->timestamp('first_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_runs');
    }
};
