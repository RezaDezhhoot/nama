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
            $table->boolean('show_letter')->default(true);
            $table->boolean('show_area_interface')->default(true);
            $table->boolean('show_images')->default(true);

            $table->boolean('report_video_required')->default(false);
            $table->boolean('report_other_video_required')->default(false);
            $table->boolean('report_images_required')->default(false);
            $table->boolean('report_images2_required')->default(false);

            $table->boolean('show_report_video')->default(true);
            $table->boolean('show_report_other_video')->default(true);
            $table->boolean('show_report_images')->default(true);
            $table->boolean('show_report_images2')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_plans', function (Blueprint $table) {
            $table->dropColumn([
                'show_letter','show_area_interface','show_images',
                'report_video_required','report_other_video_required','report_images_required','report_images2_required',
                'show_report_video','show_report_other_video','show_report_images','show_report_images2'
            ]);
        });
    }
};
