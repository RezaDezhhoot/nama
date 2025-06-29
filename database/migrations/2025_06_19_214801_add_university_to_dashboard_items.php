<?php

use App\Models\DashboardItem;
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
        DashboardItem::query()->firstOrCreate([
           [ 'type' , \App\Enums\UnitType::UNIVERSITY]
        ] , [
            'title' => 'دانشگاه',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::UNIVERSITY
        ]);
    }
};
