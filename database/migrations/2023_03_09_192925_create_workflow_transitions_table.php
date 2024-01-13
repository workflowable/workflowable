<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;

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
            $table->foreignIdFor(WorkflowActivity::class, 'from_workflow_activity_id')
                ->nullable()
                ->constrained('workflow_activities')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivity::class, 'to_workflow_activity_id')
                ->constrained('workflow_activities')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('ordinal')
                ->comment('This is used to determine the order the transitions are evaluated.');
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
