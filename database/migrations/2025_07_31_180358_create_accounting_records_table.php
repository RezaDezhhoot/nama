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
        Schema::create('accounting_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('accounting_batch_id')->index();
            $table->string('sheba')->nullable();
            $table->foreignId('unit_id')->index();
            $table->foreignId('region_id')->index();
            $table->string('type')->index();
            $table->longText('records')->nullable();
            $table->string('unit_type')->index();
            $table->string('unit_sub_type')->index()->nullable();
            $table->unsignedBigInteger('requests_and_reports')->default(0);
            $table->unsignedBigInteger('students')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_records');
    }
};
