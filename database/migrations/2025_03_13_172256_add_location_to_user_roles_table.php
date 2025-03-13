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
        Schema::table('user_roles', function (Blueprint $table) {
            $table->foreignId('city_id')->index()->nullable();
            $table->foreignId('region_id')->index()->nullable();
            $table->foreignId('neighborhood_id')->index()->nullable();
            $table->foreignId('area_id')->index()->nullable();
            $table->boolean('auto_accept')->default(false);
            $table->decimal("lat",40,12)->nullable();
            $table->decimal("lng",40,12)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropColumn(['city_id','region_id','neighborhood_id','area_id','auto_accept','lat','lng']);
        });
    }
};
