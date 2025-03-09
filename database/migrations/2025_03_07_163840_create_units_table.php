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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('sub_type')->nullable();
            $table->foreignId('parent_id')->index()->nullable();
            $table->foreignId('city_id')->index()->nullable();
            $table->foreignId('region_id')->index()->nullable();
            $table->foreignId('neighborhood_id')->index()->nullable();
            $table->foreignId('area_id')->index()->nullable();
            $table->boolean('auto_accept')->default(false);
            $table->decimal("lat",40,12)->nullable();
            $table->decimal("lng",40,12)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
