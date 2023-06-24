<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Workflowable\WorkflowEngine\Models\WorkflowPriority;

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
            $table->foreignIdFor(WorkflowPriority::class);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_priorities');
        Schema::table('workflows', function(Blueprint $table) {
            $table->dropForeignIdFor(WorkflowPriority::class, 'workflow_priority_id');
            $table->dropColumn('workflow_priority_id');
        });
    }
};
