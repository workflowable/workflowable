<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_activity_type_workflow_event', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowEvent::class, 'workflow_event_id')
                ->constrained(null, 'id', 'wewat_workflow_event_constraint')
                ->onDelete('cascade');
            $table->foreignIdFor(WorkflowActivityType::class, 'workflow_activity_type_id')
                ->constrained(null, 'id', 'wewat_workflow_activity_type_constraint')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['workflow_event_id', 'workflow_activity_type_id'], 'workflow_event_id_workflow_activity_type_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_activity_type_workflow_event');
    }
};
