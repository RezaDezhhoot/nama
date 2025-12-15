<?php

use App\Models\Unit;
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
        $ignore = [];
        Unit::query()
            ->whereNull('systematic_code')
            ->chunkById(100 , function ($items) use (&$ignore) {
                $values = [];
                foreach ($items as $item) {
                    $id = Unit::generateCode(ignore: $ignore);
                    $ignore[] = $id;
                    $values[] = [
                        'id' => $item->id,
                        'systematic_code' => $id
                    ];
                }
                batch()->update(new Unit, $values, 'id');
            })
        ;
    }
};
