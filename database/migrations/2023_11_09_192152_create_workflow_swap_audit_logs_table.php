<?php

namespace Workflowable\Swaps\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_swap_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowSwap::class, 'workflow_swap_id')
                ->constrained('workflow_swaps');

            $table->foreignIdFor(WorkflowProcess::class, 'from_workflow_process_id')
                ->constrained('workflow_processes');
            $table->foreignIdFor(WorkflowActivity::class, 'from_workflow_activity_id')
                ->constrained('workflow_activities');

            $table->foreignIdFor(WorkflowProcess::class, 'to_workflow_process_id')
                ->constrained('workflow_processes');
            $table->foreignIdFor(WorkflowActivity::class, 'to_workflow_activity_id')
                ->nullable()
                ->comment('When no activity is provided to transition to, we will start over from the beginning')
                ->constrained('workflow_activities');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_swap_audit_logs');
    }
};
