<?php

namespace Workflowable\Swaps\Database\Migrations;

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
        Schema::table('workflow_events', function (Blueprint $table) {
            $table->string('class_name')->after('alias')->unique();
            $table->dropUnique('workflow_events_alias_unique');
            $table->dropColumn('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_events', function (Blueprint $table) {
            $table->string('alias')->after('class_name')->unique();
            $table->dropUnique('class_name');
            $table->dropColumn('class_name');
        });
    }
};
