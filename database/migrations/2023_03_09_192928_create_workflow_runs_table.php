<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowAction;
use Workflowable\Workflow\Models\WorkflowRunStatus;

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
                ->constrained('workflow_run_statuses');
            $table->foreignIdFor(WorkflowAction::class, 'last_workflow_action_id')
                ->nullable()
                ->constrained('workflow_actions');
            $table->json('parameters');
            $table->timestamp('first_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->useCurrent();
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
