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
        Schema::create('rings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('national_code' , 10)->nullable();
            $table->timestamp('birthdate')->nullable();
            $table->string('postal_code' , 10)->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('level_of_education')->nullable();
            $table->string('field_of_study')->nullable();
            $table->json('functional_area')->nullable();
            $table->json('skill_area')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('item_id')->nullable()->index();
            $table->string('job')->nullable();
            $table->string('sheba_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rings');
    }
};
