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
            $table->string('gender')->index()->nullable();
            $table->boolean('armani')->default(true);
            $table->foreignId('state_id')->index()->nullable();
            $table->string('postal_code')->nullable();
            $table->string('responsible')->nullable();
            $table->string('responsible_phone')->nullable();
            $table->string('tell')->nullable();
            $table->string('scope_activity')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('from_age')->nullable();
            $table->unsignedTinyInteger('to_age')->nullable();
            $table->fullText(['title'],'title_search');
            $table->string('systematic_code')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn([
                'gender' , 'armani' , 'state_id' ,'postal_code' ,'responsible' , 'responsible_phone' , 'tell' ,'scope_activity',
                'description' ,'from_age','to_age' , 'systematic_code'
            ]);
            $table->dropFullText('title_search');
        });
    }
};
