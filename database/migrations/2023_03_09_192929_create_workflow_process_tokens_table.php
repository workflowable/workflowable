<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_process_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorkflowProcess::class, 'workflow_process_id')
                ->constrained('workflow_processes')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowActivity::class, 'workflow_activity_id')
                ->nullable()
                ->constrained('workflow_activities')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('value', 255);
            $table->timestamps();

            $table->index(['key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_process_tokens');
    }
};
