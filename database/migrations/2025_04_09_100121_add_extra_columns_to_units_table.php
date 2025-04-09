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
        Schema::table('units', function (Blueprint $table) {
            $table->string('code')->nullable();

            $table->string('phone1')->nullable();
            $table->string('phone1_title')->nullable();

            $table->string('phone2')->nullable();
            $table->string('phone2_title')->nullable();

            $table->string('phone3')->nullable();
            $table->string('phone3_title')->nullable();

            $table->string('phone4')->nullable();
            $table->string('phone4_title')->nullable();

            $table->string('phone5')->nullable();
            $table->string('phone5_title')->nullable();

            $table->string('phone6')->nullable();
            $table->string('phone6_title')->nullable();

            $table->string('phone7')->nullable();
            $table->string('phone7_title')->nullable();

            $table->string('phone8')->nullable();
            $table->string('phone8_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'phone1','phone1_title',
                'phone2','phone2_title',
                'phone3','phone3_title',
                'phone4','phone4_title',
                'phone5','phone5_title',
                'phone6','phone6_title',
                'phone7','phone7_title',
                'phone8','phone8_title',
            ]);
        });
    }
};
