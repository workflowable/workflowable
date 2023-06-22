<?php

namespace Workflowable\WorkflowEngine\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignIdFor(WorkflowEvent::class, 'workflow_event_id')
                ->constrained('workflow_events')
                ->cascadeOnDelete();
            $table->foreignIdFor(WorkflowStatus::class, 'workflow_status_id')
                ->constrained('workflow_statuses')
                ->cascadeOnDelete();
            $table->unsignedInteger('retry_interval')->default(300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
