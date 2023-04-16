<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStepType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowStepType::class, 'workflow_step_type_id')
                ->constrained('workflow_step_types');
            $table->foreignIdFor(Workflow::class, 'workflow_id')
                ->constrained('workflows');
            $table->string('friendly_name');
            $table->string('description')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_actions');
    }
};
