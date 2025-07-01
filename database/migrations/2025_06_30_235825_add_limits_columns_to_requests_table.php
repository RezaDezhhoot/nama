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
        Schema::table('requests', function (Blueprint $table) {
            $table->timestamp('auto_accept_at')->nullable();
            $table->timestamp('next_notify_at')->nullable();
            $table->unsignedTinyInteger('auto_accept_period')->nullable();
            $table->unsignedTinyInteger('notify_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['auto_accept_at','next_notify_at']);
            $table->dropColumn(['auto_accept_period','notify_period']);
        });
    }
};
