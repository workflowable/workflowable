<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Database\Seeders\WorkflowActivityAttemptStatusSeeder;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityAttemptStatus;
use Workflowable\Workflowable\Models\WorkflowProcess;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_activity_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowProcess::class, 'workflow_process_id')
                ->comment('The workflow run we completed the activity on')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivity::class, 'workflow_activity_id')
                ->comment('The activity that was completed')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivityAttemptStatus::class, 'workflow_activity_attempt_status_id')
                ->comment('The status of the attempt')
                ->constrained(null, 'id', 'workflow_activity_attempt_status')
                ->cascadeOnDelete();

            $table->dateTime('started_at');
            $table->datetime('completed_at');
            $table->timestamps();
        });

        (new WorkflowActivityAttemptStatusSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_activity_attempts');
    }
};
