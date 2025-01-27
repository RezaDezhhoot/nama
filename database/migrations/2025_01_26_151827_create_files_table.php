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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable');
            $table->integer('position')->default(0);
            $table->string('path')->index();
            $table->string('mime_type',100)->nullable();
            $table->bigInteger('size')->comment('bytes')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('disk',100)->nullable();
            $table->json('data')->nullable();
            $table->bigInteger('duration')->default(0)->nullable()->comment('sec');
            $table->string('status',50);
            $table->string('subject',50)->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
