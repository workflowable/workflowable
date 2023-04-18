<?php

namespace Workflowable\Workflow\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflow\Models\WorkflowEvent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_condition_types', function (Blueprint $table) {
            $table->id();
            $table->string('friendly_name');
            $table->string('alias')->unique();
            $table->foreignIdFor(WorkflowEvent::class, 'workflow_event_id')
                ->nullable()
                ->constrained('workflow_events');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_condition_types');
    }
};
