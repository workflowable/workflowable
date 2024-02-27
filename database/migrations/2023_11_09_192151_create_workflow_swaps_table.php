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
            $table->foreignIdFor(Workflow::class, 'from_workflow_id')->constrained('workflows');
            $table->foreignIdFor(Workflow::class, 'to_workflow_id')->constrained('workflows');
            $table->foreignIdFor(WorkflowSwapStatus::class, 'workflow_swap_status_id')->constrained('workflow_swap_statuses');
            $table->boolean('should_transfer_output_tokens')->default(true);
            $table->dateTime('scheduled_at')->nullable()->index()->comment('Used for scheduling a workflow swap for a date and time in the future');
            $table->dateTime('dispatched_at')->nullable()->index()->comment('Indicates when the system dispatched the job to process the swap');
            $table->dateTime('started_at')->nullable()->index()->comment('Indicates the time the system was actually able to begin working on the swap');
            $table->dateTime('completed_at')->nullable()->index()->comment('Indicates when we have completed a workflow swap');
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
