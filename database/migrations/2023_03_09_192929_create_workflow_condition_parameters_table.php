<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowCondition;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_condition_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowCondition::class, 'workflow_condition_id')
                ->nullable()
                ->constrained('workflow_conditions')
                ->cascadeOnDelete();
            $table->string('key');
            $table->text('value');

            $table->unique(['workflow_condition_id', 'key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_condition_parameters');
    }
};
