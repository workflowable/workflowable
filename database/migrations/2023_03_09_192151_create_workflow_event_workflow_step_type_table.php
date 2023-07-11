<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStepType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_event_workflow_step_type', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowEvent::class, 'workflow_event_id')
                ->constrained(null, 'id', 'wewst_workflow_event_constraint')
                ->onDelete('cascade');
            $table->foreignIdFor(WorkflowStepType::class, 'workflow_step_type_id')
                ->constrained(null, 'id', 'wewst_workflow_step_type_constraint')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['workflow_event_id', 'workflow_step_type_id'], 'workflow_event_id_workflow_step_type_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_event_workflow_step_type');
    }
};
