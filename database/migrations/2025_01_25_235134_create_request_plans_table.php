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
        Schema::create('request_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('image');

            $table->string('status')->index();
            $table->string('sub_title')->nullable();
            $table->integer('max_number_people_supported')->nullable();
            $table->integer('support_for_each_person_amount')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->tinyInteger('max_allocated_request')->nullable();
            $table->text('body')->nullable();
            $table->boolean('bold')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_plans');
    }
};
