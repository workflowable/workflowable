<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowStep;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_run_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowRun::class, 'workflow_run_id')
                ->constrained('workflow_runs')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowStep::class, 'workflow_step_id')
                ->nullable()
                ->constrained('workflow_steps')
                ->cascadeOnDelete();
            $table->string('key');
            $table->text('value');
            $table->string('type');
            $table->timestamps();

            $table->index(['key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_run_parameters');
    }
};
