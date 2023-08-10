<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowActivity;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_activity_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowActivity::class, 'workflow_activity_id')
                ->nullable()
                ->constrained('workflow_activities')
                ->cascadeOnDelete();
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_activity_parameters');
    }
};
