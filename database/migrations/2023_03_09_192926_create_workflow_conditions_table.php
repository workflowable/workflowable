<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowTransition;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowTransition::class, 'workflow_transition_id')
                ->constrained('workflow_transitions')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowConditionType::class, 'workflow_condition_type_id')
                ->constrained('workflow_condition_types')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('ordinal')
                ->comment('This is used to determine the order the conditions are evaluated.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_conditions');
    }
};
