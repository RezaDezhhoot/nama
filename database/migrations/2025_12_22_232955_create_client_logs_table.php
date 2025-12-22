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
        Schema::create('client_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('context')->nullable();
            $table->string('agent')->nullable();
            $table->string('ip')->index()->nullable();
            $table->json('headers')->nullable();
            $table->string('client_version')->nullable();
            $table->string('platform')->nullable();
            $table->foreignId('user_id')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_logs');
    }
};
