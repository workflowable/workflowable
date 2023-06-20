<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Workflow::class, 'workflow_id')
                ->constrained('workflows')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowStep::class, 'from_workflow_step_id')
                ->nullable()
                ->constrained('workflow_steps')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowStep::class, 'to_workflow_step_id')
                ->constrained('workflow_steps')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('ordinal')
                ->comment('This is used to determine the order the transitions are evaluated.');
            $table->uuid('ux_uuid')
                ->nullable()
                ->comment('This is used to identify the transition in the UI.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
