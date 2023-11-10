<?php

namespace Workflowable\Swaps\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowSwapStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Workflow::class, 'from_workflow_id')->constrained();
            $table->foreignIdFor(Workflow::class, 'to_workflow_id')->constrained();
            $table->foreignIdFor(WorkflowSwapStatus::class, 'workflow_swap_status_id')
                ->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_swaps');
    }
};
