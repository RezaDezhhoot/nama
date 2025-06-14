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
        Schema::create('ring_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ring_id')->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('national_code' , 10)->nullable();
            $table->timestamp('birthdate')->nullable();
            $table->string('postal_code' , 10)->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('father_name')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ring_members');
    }
};
