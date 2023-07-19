<?php

namespace Workflowable\Workflowable\Database\Migrations;

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
        Schema::create('workflow_configuration_parameters', function (Blueprint $table) {
            $table->id();
            $table->morphs('parameterizable', 'parameterizable_index');
            $table->string('key');
            $table->string('value');
            $table->timestamps();

            $table->index(['key', 'value'], 'parameterizable_key_value_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_configuration_parameters');
    }
};
