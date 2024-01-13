<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivityType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowActivityType::class, 'workflow_activity_type_id')
                ->constrained('workflow_activity_types')
                ->cascadeOnDelete();
            $table->foreignIdFor(Workflow::class, 'workflow_id')
                ->constrained('workflows')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_activities');
    }
};
