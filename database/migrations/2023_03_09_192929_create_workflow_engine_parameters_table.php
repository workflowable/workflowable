<?php

namespace Workflowable\WorkflowEngine\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_engine_parameters', function (Blueprint $table) {
            $table->id();
            $table->morphs('parameterizable');
            $table->string('key');
            $table->string('value');
            $table->timestamps();

            $table->index(['parameterizable_id', 'parameterizable_type', 'key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_engine_parameters');
    }
};
