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
        Schema::create('written_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status')->index();
            $table->string('step')->index();
            $table->text('body')->nullable();
            $table->boolean('countable')->default(false)->index();
            $table->foreignId('user_id')->index();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('written_requests');
    }
};
