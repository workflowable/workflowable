<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowRun::class, 'workflow_run_id')->constrained('workflow_runs');
            $table->morphs('sourceable');
            $table->foreignIdFor(WorkflowRunStatus::class, 'workflow_run_status_id')->constrained('workflow_run_statuses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_signals');
    }
};
