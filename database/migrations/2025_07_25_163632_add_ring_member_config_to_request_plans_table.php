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
        Schema::table('request_plans', function (Blueprint $table) {
            $table->boolean("ring_member_required")->default(false);
            $table->boolean("show_ring_member")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_plans', function (Blueprint $table) {
            $table->dropColumn(['ring_member_required','show_ring_member']);
        });
    }
};
