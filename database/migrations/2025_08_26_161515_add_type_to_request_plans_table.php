<?php

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
        Schema::table('request_plans', function (Blueprint $table) {
            $table->string('type')->nullable()->index();
        });
        \App\Models\RequestPlan::query()->withTrashed()->update([
            'type' => \App\Enums\PlanTypes::DEFAULT->value
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_plans', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
