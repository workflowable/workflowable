<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowRun;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_activity_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowRun::class, 'workflow_run_id')
                ->comment('The workflow run we completed the activity on')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivity::class, 'workflow_activity_id')
                ->comment('The activity that was completed')
                ->constrained()
                ->cascadeOnDelete();

            $table->dateTime('started_at');
            $table->datetime('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_activity_completions');
    }
};
