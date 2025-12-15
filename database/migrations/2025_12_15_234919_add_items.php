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
            [ 'type' , \App\Enums\UnitType::GARDEN]
        ] , [
            'title' => 'بوستان',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::GARDEN,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::HALL]
        ] , [
            'title' => 'سرا',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::HALL,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::STADIUM]
        ] , [
            'title' => 'ورزشگاه',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::STADIUM,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::DARUL_QURAN]
        ] , [
            'title' => 'دارالقرآن',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::DARUL_QURAN,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::CULTURAL_INSTITUTE]
        ] , [
            'title' => 'موسسه فرهنگی',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::CULTURAL_INSTITUTE,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::SEMINARY]
        ] , [
            'title' => 'حوزه علمیه',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::SEMINARY,
            'status' => false
        ]);
        DashboardItem::query()->firstOrCreate([
            [ 'type' , \App\Enums\UnitType::QURANIC_CENTER]
        ] , [
            'title' => 'مرکز قرآنی',
            'body' => 'ورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، متنوع با هدف بهبود کاربردی.',
            'color' => '#c33232',
            'logo' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'image' => '/storage/bKozUkFJYsO5TPqbP4k9JdrYiXTKz8iUpe8l6Y0P.png',
            'type' => \App\Enums\UnitType::QURANIC_CENTER,
            'status' => false
        ]);
    }
};
