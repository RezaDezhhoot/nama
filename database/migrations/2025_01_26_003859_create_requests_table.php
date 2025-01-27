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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreignId('request_plan_id')->index();
            $table->string('step')->index();
            $table->string('status')->index();

            $table->unsignedInteger('students')->nullable();
            $table->unsignedBigInteger('total_amount')->nullable();
            $table->timestamp('date')->nullable();

            $table->text('body')->nullable();

            $table->boolean('confirm')->default(false);
            $table->integer('amount')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
