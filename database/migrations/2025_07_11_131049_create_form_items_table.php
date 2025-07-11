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
        Schema::create('form_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->nullable();
            $table->string('title')->nullable();
            $table->boolean('required')->default(false);
            $table->string('type')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help')->nullable();
            $table->string('max')->nullable();
            $table->string('min')->nullable();
            $table->string('mime_types')->nullable();
            $table->json('options')->nullable();
            $table->json('conditions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_items');
    }
};
