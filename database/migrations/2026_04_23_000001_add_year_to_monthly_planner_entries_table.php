<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_planner_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->nullable()->after('month_label');
            $table->index(['user_id', 'year', 'month_label']);
        });
    }

    public function down(): void
    {
        Schema::table('monthly_planner_entries', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'year', 'month_label']);
            $table->dropColumn('year');
        });
    }
};

