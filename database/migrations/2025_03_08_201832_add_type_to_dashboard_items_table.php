<?php

use App\Models\DashboardItem;
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
        Schema::table('dashboard_items', function (Blueprint $table) {
            $table->string('type')->unique()->nullable();
        });
        DashboardItem::query()->where('id',2)->update([
            'type' => \App\Enums\UnitType::MOSQUE
        ]);
        DashboardItem::query()->where('id',3)->update([
            'type' => \App\Enums\UnitType::SCHOOL
        ]);
        DashboardItem::query()->where('id',4)->update([
            'type' => \App\Enums\UnitType::CENTER
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboard_items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
