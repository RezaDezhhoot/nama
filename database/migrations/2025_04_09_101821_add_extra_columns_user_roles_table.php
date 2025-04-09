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
        Schema::table('user_roles', function (Blueprint $table) {
            $table->string('sheba1')->nullable();
            $table->string('sheba1_title')->nullable();

            $table->string('sheba2')->nullable();
            $table->string('sheba2_title')->nullable();

            $table->string('sheba3')->nullable();
            $table->string('sheba3_title')->nullable();

            $table->string('sheba4')->nullable();
            $table->string('sheba4_title')->nullable();

            $table->string('sheba5')->nullable();
            $table->string('sheba5_title')->nullable();

            $table->string('sheba6')->nullable();
            $table->string('sheba6_title')->nullable();

            $table->string('sheba7')->nullable();
            $table->string('sheba7_title')->nullable();

            $table->string('sheba8')->nullable();
            $table->string('sheba8_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropColumn([
                'sheba1','sheba1_title',
                'sheba2','sheba2_title',
                'sheba3','sheba3_title',
                'sheba4','sheba4_title',
                'sheba5','sheba5_title',
                'sheba6','sheba6_title',
                'sheba7','sheba7_title',
                'sheba8','sheba8_title',
            ]);
        });
    }
};
