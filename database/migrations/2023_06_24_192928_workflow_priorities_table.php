<?php

namespace Workflowable\Workflowable\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\Workflowable\Models\WorkflowPriority;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('priority')->unsigned();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->foreignIdFor(WorkflowPriority::class, 'workflow_priority_id')->constrained();
        });
    }

    public function down()
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(WorkflowPriority::class, 'workflow_priority_id');
        });
        Schema::dropIfExists('workflow_priorities');

    }
};
