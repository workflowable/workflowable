<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStep;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_run_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowRun::class, 'workflow_run_id')->constrained();
            $table->string('name');
            $table->string('value');
            $table->timestamps();

            $table->index(['name', 'value']);
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
