<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_condition_type_workflow_event', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowEvent::class, 'workflow_event_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(WorkflowConditionType::class, 'workflow_condition_type_id')
                ->constrained()
                ->onDelete('cascade');

            $table->unique(['workflow_event_id', 'workflow_condition_type_id'], 'workflow_event_id_workflow_condition_type_id_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_condition_type_workflow_event');
    }
};
